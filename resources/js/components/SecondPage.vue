<template>
    <div class="outfit-selector">
        <!-- Header Section -->
        <div class="header">
            <h1>Підбір одягу за кольоротипом</h1>
            <div class="color-type-info" v-if="displayResult">
                <div class="color-type-badge">
                    <span>{{ displayResult }}</span>
                </div>
            </div>
        </div>

        <div class="main-content">
            <!-- Controls Panel -->
            <div class="controls-panel">
                <!-- Color Type Section -->
                <div class="control-section">
                    <h3>Кольоротип</h3>
                    <div class="color-type-selector">
                        <button
                            v-for="type in colorTypes"
                            :key="type.name"
                            :class="{ active: selectedColorType === type.name }"
                            @click="selectColorType(type.name)"
                            class="color-type-btn"
                        >
                            {{ type.name }}
                        </button>
                    </div>
                </div>

                <!-- Clothing Mode Section -->
                <div class="control-section">
                    <h3>Частина одягу</h3>
                    <div class="toggle-group">
                        <button
                            :class="{ active: clothesMode === 'top' }"
                            @click="clothesMode = 'top'"
                            class="toggle-btn"
                        >
                            <span class="icon">👕</span>
                            <span class="label">Верх</span>
                        </button>
                        <button
                            :class="{ active: clothesMode === 'bottom' }"
                            @click="clothesMode = 'bottom'"
                            class="toggle-btn"
                        >
                            <span class="icon">👖</span>
                            <span class="label">Низ</span>
                        </button>
                    </div>
                </div>

                <!-- Recommended Colors Section -->
                <div class="control-section">
                    <h3>Рекомендовані кольори</h3>
                    <div class="color-grid">
                        <button
                            v-for="(colorInfo, colorKey) in recommendedColors"
                            :key="colorKey"
                            @click="setClothesColor(colorKey)"
                            :class="{
                active: selectedColor === colorKey,
                recommended: colorInfo.recommended
              }"
                            class="color-btn"
                            :style="{
                backgroundColor: `#${CLOTHES_COLORS[colorKey]?.toString(16).padStart(6, '0')}`
              }"
                            :title="colorInfo.name"
                        >
                            <span v-if="colorInfo.recommended" class="recommend-badge">★</span>
                        </button>
                    </div>
                </div>

                <!-- Clothing Type Section -->
                <div class="control-section">
                    <h3>Тип одягу</h3>
                    <div class="clothing-options">
                        <div class="option-group">
                            <label>Низ:</label>
                            <div class="option-buttons">
                                <button
                                    :class="{ active: showSkirt }"
                                    @click="showSkirt = true"
                                    class="option-btn"
                                >
                                    Спідниця
                                </button>
                                <button
                                    :class="{ active: !showSkirt }"
                                    @click="showSkirt = false"
                                    class="option-btn"
                                >
                                    Штани
                                </button>
                            </div>
                        </div>

                        <div class="option-group">
                            <label>Верх:</label>
                            <div class="option-buttons">
                                <button
                                    :class="{ active: showTShirt }"
                                    @click="showTShirt = true"
                                    class="option-btn"
                                >
                                    Футболка
                                </button>
                                <button
                                    :class="{ active: !showTShirt }"
                                    @click="showTShirt = false"
                                    class="option-btn"
                                >
                                    Блузка
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Style Tips -->
                <div class="control-section">
                    <h3>Поради стиліста</h3>
                    <div class="style-tips">
                        <p v-for="tip in getCurrentTips()" :key="tip" class="tip">
                            💡 {{ tip }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3D Model Section -->
            <div class="model-section">
                <div class="model-container">
                    <div class="model-box" ref="modelBox">
                        <div v-if="isLoading" class="loading-overlay">
                            <div class="loading-spinner"></div>
                            <p>Завантаження моделі...</p>
                        </div>
                        <div v-if="loadError" class="error-overlay">
                            <p>❌ Помилка завантаження моделей</p>
                            <small>{{ loadError }}</small>
                        </div>
                    </div>

                    <div class="model-controls">
                        <button @click="resetCamera" class="control-btn">
                            🔄 Скинути
                        </button>
                        <button @click="toggleWireframe" class="control-btn">
                            {{ wireframe ? '🎭 Звичайний' : '📐 Каркас' }}
                        </button>
                    </div>
                </div>

                <!-- Color Analysis -->
                <div class="color-analysis">
                    <h4>Аналіз кольору</h4>
                    <div class="analysis-card">
                        <div class="color-swatch" :style="{ backgroundColor: currentColorHex }"></div>
                        <div class="color-info">
                            <p><strong>Поточний:</strong> {{ currentColorName }}</p>
                            <p><strong>Рекомендація:</strong>
                                <span :class="{
                  'good': isColorRecommended,
                  'warning': !isColorRecommended
                }">
                  {{ isColorRecommended ? 'Підходить!' : 'Спробуйте інший' }}
                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import * as THREE from 'three'
import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader.js'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'

const CLOTHES_COLORS = {
    red: 0xff0000,
    orange: 0xffa500,
    yellow: 0xffeb3b,
    green: 0x4caf50,
    blue: 0x2196f3,
    indigo: 0x3f51b5,
    violet: 0x8e24aa,
    grey: 0x888888,
    pink: 0xff69b4,
    coral: 0xff7f50,
    navy: 0x000080,
    burgundy: 0x800020
}

const COLOR_TYPES = {
    'Весна': {
        colors: ['orange', 'yellow', 'coral', 'green'],
        tips: [
            'Обирайте теплі та яскраві кольори',
            'Уникайте чорного та сірого',
            'Персиковий та золотистий - ваші кольори'
        ]
    },
    'Літо': {
        colors: ['blue', 'pink', 'grey', 'violet'],
        tips: [
            'Холодні пастельні відтінки вам підходять',
            'Рожевий та лавандовий - ідеальний вибір',
            'Уникайте надто яскравих кольорів'
        ]
    },
    'Осінь': {
        colors: ['orange', 'burgundy', 'yellow', 'green'],
        tips: [
            'Теплі землисті тони - ваша сила',
            'Гірчичний та теракотовий підкреслять красу',
            'Уникайте холодних відтінків'
        ]
    },
    'Зима': {
        colors: ['red', 'navy', 'grey', 'indigo'],
        tips: [
            'Контрастні чисті кольори - ваш стиль',
            'Чорний, білий, яскраво-червоний підходять',
            'Уникайте приглушених тонів'
        ]
    }
}

export default {
    name: 'OutfitSelector',
    props: {
        userColorType: {
            type: String,
            default: null
        },
        colorTypeResult: {
            type: String,
            default: null
        }
    },
    setup(props) {
        const getInitialColorType = () => {
            if (props.colorTypeResult) return extractColorTypeFromResult(props.colorTypeResult)
            if (props.userColorType) return props.userColorType

            const result = localStorage.getItem("result")
            if (result) return extractColorTypeFromResult(result)

            return 'Весна'
        }

        const extractColorTypeFromResult = (result) => {
            if (!result) return 'Весна'

            const resultLower = result.toLowerCase()
            if (resultLower.includes('весна')) return 'Весна'
            if (resultLower.includes('літо')) return 'Літо'
            if (resultLower.includes('осінь')) return 'Осінь'
            if (resultLower.includes('зима')) return 'Зима'

            return 'Весна'
        }

        const modelBox = ref(null)
        const isLoading = ref(true)
        const loadError = ref(null)
        const clothesMode = ref('bottom')
        const showSkirt = ref(true)
        const showTShirt = ref(true)
        const selectedColor = ref('grey')
        const selectedColorType = ref(getInitialColorType())
        const wireframe = ref(false)
        const colorTypeResult = ref(props.colorTypeResult || '')

        const skirtMeshRef = ref(null)
        const pantsMeshRef = ref(null)
        const tshirtMeshRef = ref(null)
        const blouseMeshRef = ref(null)

        let scene, camera, renderer, controls, animationId

        const colorType = computed(() => selectedColorType.value)

        const displayResult = computed(() => {
            return colorTypeResult.value || `${selectedColorType.value} - рекомендовані кольори`
        })

        const colorTypes = computed(() =>
            Object.keys(COLOR_TYPES).map(name => ({ name }))
        )

        const recommendedColors = computed(() => {
            const recommended = COLOR_TYPES[selectedColorType.value]?.colors || []
            const result = {}

            Object.keys(CLOTHES_COLORS).forEach(colorKey => {
                result[colorKey] = {
                    name: colorKey.charAt(0).toUpperCase() + colorKey.slice(1),
                    recommended: recommended.includes(colorKey)
                }
            })

            return result
        })

        const currentColorHex = computed(() =>
            `#${CLOTHES_COLORS[selectedColor.value]?.toString(16).padStart(6, '0')}`
        )

        const currentColorName = computed(() =>
            selectedColor.value.charAt(0).toUpperCase() + selectedColor.value.slice(1)
        )

        const isColorRecommended = computed(() =>
            recommendedColors.value[selectedColor.value]?.recommended || false
        )

        const selectColorType = (type) => {
            selectedColorType.value = type
        }

        const setClothesColor = (colorName) => {
            selectedColor.value = colorName
            const color = CLOTHES_COLORS[colorName]

            if (clothesMode.value === 'bottom') {
                if (skirtMeshRef.value) skirtMeshRef.value.material.color.set(color)
                if (pantsMeshRef.value) pantsMeshRef.value.material.color.set(color)
            } else {
                if (tshirtMeshRef.value) tshirtMeshRef.value.material.color.set(color)
                if (blouseMeshRef.value) blouseMeshRef.value.material.color.set(color)
            }
        }

        const getCurrentTips = () => {
            return COLOR_TYPES[selectedColorType.value]?.tips || []
        }

        const resetCamera = () => {
            if (camera && controls) {
                camera.position.set(0, 0, 2)
                controls.target.set(0, 0, 0)
                controls.update()
            }
        }

        const toggleWireframe = () => {
            wireframe.value = !wireframe.value
                [skirtMeshRef.value, pantsMeshRef.value, tshirtMeshRef.value, blouseMeshRef.value].forEach(mesh => {
                if (mesh && mesh.material) {
                    mesh.material.wireframe = wireframe.value
                }
            })
        }

        const animate = () => {
            animationId = requestAnimationFrame(animate)

            if (skirtMeshRef.value) skirtMeshRef.value.parent.visible = showSkirt.value
            if (pantsMeshRef.value) pantsMeshRef.value.parent.visible = !showSkirt.value
            if (tshirtMeshRef.value) tshirtMeshRef.value.parent.visible = showTShirt.value
            if (blouseMeshRef.value) blouseMeshRef.value.parent.visible = !showTShirt.value

            if (controls) controls.update()
            if (renderer && scene && camera) renderer.render(scene, camera)
        }

        const init3D = () => {
            if (!modelBox.value) return

            scene = new THREE.Scene()
            camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000)
            camera.position.z = 2

            renderer = new THREE.WebGLRenderer({ antialias: true })
            const size = Math.min(modelBox.value.clientWidth, 350)
            renderer.setSize(size, size)
            renderer.setClearColor(0xf0f0f0)
            modelBox.value.appendChild(renderer.domElement)

            controls = new OrbitControls(camera, renderer.domElement)
            controls.enableDamping = true
            controls.dampingFactor = 0.05
            controls.enablePan = false
            controls.screenSpacePanning = false
            controls.minDistance = 0.5
            controls.maxDistance = 10
            controls.target.set(0, 0, 0)
            controls.minPolarAngle = Math.PI / 2
            controls.maxPolarAngle = Math.PI / 2
            controls.update()

            const light = new THREE.DirectionalLight(0xffffff, 1)
            light.position.set(1, 1, 1).normalize()
            scene.add(light)

            loadModels()
        }

        const loadModels = () => {
            const loader = new OBJLoader()

            loader.load(
                '/models/avatar.obj',
                function (avatar) {
                    avatar.traverse(child => {
                        if (child.isMesh) {
                            child.material = new THREE.MeshPhongMaterial({ color: 0xffdbac })
                        }
                    })
                    const box = new THREE.Box3().setFromObject(avatar)
                    const size = box.getSize(new THREE.Vector3())
                    const scale = 1 / Math.max(size.x, size.y, size.z)
                    avatar.scale.set(scale, scale, scale)
                    box.setFromObject(avatar)
                    const center = box.getCenter(new THREE.Vector3())
                    avatar.position.sub(center)
                    scene.add(avatar)

                    loader.load(
                        '/models/skirt2.obj',
                        function (skirt) {
                            skirt.traverse(child => {
                                if (child.isMesh) {
                                    child.material = new THREE.MeshPhongMaterial({ color: CLOTHES_COLORS[selectedColor.value] })
                                    skirtMeshRef.value = child
                                }
                            })
                            skirt.scale.set(scale * 1.15, scale * 1.15, scale * 1.15)
                            const box2 = new THREE.Box3().setFromObject(skirt)
                            const center2 = box2.getCenter(new THREE.Vector3())
                            skirt.position.sub(center2)
                            skirt.position.y += 20 * scale
                            skirt.position.z -= 0.003
                            scene.add(skirt)

                            loader.load(
                                '/models/pants.obj',
                                function (pants) {
                                    pants.traverse(child => {
                                        if (child.isMesh) {
                                            child.material = new THREE.MeshPhongMaterial({ color: CLOTHES_COLORS[selectedColor.value] })
                                            pantsMeshRef.value = child
                                        }
                                    })
                                    pants.scale.set(scale, scale, scale)
                                    const boxP = new THREE.Box3().setFromObject(pants)
                                    const centerP = boxP.getCenter(new THREE.Vector3())
                                    pants.position.sub(centerP)
                                    pants.position.y += (box.max.y - box.min.y) * -0.16
                                    pants.position.z += 0.00075
                                    scene.add(pants)
                                    pants.visible = false

                                    loader.load(
                                        '/models/tshirt2.obj',
                                        function (tshirt) {
                                            tshirt.traverse(child => {
                                                if (child.isMesh) {
                                                    child.material = new THREE.MeshPhongMaterial({ color: CLOTHES_COLORS[selectedColor.value] })
                                                    tshirtMeshRef.value = child
                                                }
                                            })
                                            tshirt.scale.set(scale * 1.12, scale * 1.12, scale * 1.12)
                                            const box3 = new THREE.Box3().setFromObject(tshirt)
                                            const center3 = box3.getCenter(new THREE.Vector3())
                                            tshirt.position.sub(center3)
                                            tshirt.position.y += (box.max.y - box.min.y) * 0.223
                                            tshirt.position.z += 0.01
                                            scene.add(tshirt)
                                            tshirt.visible = showTShirt.value

                                            loader.load(
                                                '/models/blouse.obj',
                                                function (blouse) {
                                                    blouse.traverse(child => {
                                                        if (child.isMesh) {
                                                            child.material = new THREE.MeshPhongMaterial({ color: CLOTHES_COLORS[selectedColor.value] })
                                                            blouseMeshRef.value = child
                                                        }
                                                    })
                                                    blouse.scale.set(scale * 1.12, scale * 1.12, scale * 1.12)
                                                    const box4 = new THREE.Box3().setFromObject(blouse)
                                                    const center4 = box4.getCenter(new THREE.Vector3())
                                                    blouse.position.sub(center4)
                                                    blouse.position.y += (box.max.y - box.min.y) * 0.245
                                                    blouse.position.z += 0.01
                                                    scene.add(blouse)
                                                    blouse.visible = !showTShirt.value
                                                    isLoading.value = false
                                                    animate()
                                                },
                                                undefined,
                                                function (error) {
                                                    console.error('Error loading blouse.obj:', error)
                                                    loadError.value = 'Не вдалося завантажити blouse.obj'
                                                    isLoading.value = false
                                                }
                                            )
                                        },
                                        undefined,
                                        function (error) {
                                            console.error('Error loading tshirt2.obj:', error)
                                            loadError.value = 'Не вдалося завантажити tshirt2.obj'
                                            isLoading.value = false
                                        }
                                    )
                                },
                                undefined,
                                function (error) {
                                    console.error('Error loading pants.obj:', error)
                                    loadError.value = 'Не вдалося завантажити pants.obj'
                                    isLoading.value = false
                                }
                            )
                        },
                        undefined,
                        function (error) {
                            console.error('Error loading skirt2.obj:', error)
                            loadError.value = 'Не вдалося завантажити skirt2.obj'
                            isLoading.value = false
                        }
                    )
                },
                undefined,
                function (error) {
                    console.error('Error loading avatar.obj:', error)
                    loadError.value = 'Не вдалося завантажити avatar.obj'
                    isLoading.value = false
                }
            )
        }

        onMounted(() => {
            init3D()
            window.addEventListener('resize', handleResize)
        })

        onUnmounted(() => {
            if (animationId) cancelAnimationFrame(animationId)
            if (renderer && renderer.domElement && modelBox.value) {
                modelBox.value.removeChild(renderer.domElement)
                renderer.dispose()
            }
            window.removeEventListener('resize', handleResize)
        })

        const handleResize = () => {
            if (renderer && modelBox.value && camera) {
                const size = Math.min(modelBox.value.clientWidth, 350)
                renderer.setSize(size, size)
                camera.aspect = 1
                camera.updateProjectionMatrix()
            }
        }

        watch(() => props.userColorType, (newType) => {
            if (newType) selectedColorType.value = newType
        })

        watch(() => props.colorTypeResult, (newResult) => {
            if (newResult) {
                colorTypeResult.value = newResult
                selectedColorType.value = extractColorTypeFromResult(newResult)
            }
        })

        return {
            modelBox,
            isLoading,
            loadError,
            clothesMode,
            showSkirt,
            showTShirt,
            selectedColor,
            selectedColorType,
            wireframe,
            colorType,
            displayResult,
            colorTypes,
            recommendedColors,
            currentColorHex,
            currentColorName,
            isColorRecommended,
            CLOTHES_COLORS,
            selectColorType,
            setClothesColor,
            getCurrentTips,
            resetCamera,
            toggleWireframe
        }
    }
}
</script>

<style scoped>
* {
    box-sizing: border-box;
}

.outfit-selector {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px;
    overflow-x: hidden;
}

.header {
    text-align: center;
    margin-bottom: 20px;
    color: white;
    padding: 0 10px;
}

.header h1 {
    font-size: clamp(1.3rem, 5vw, 2.5rem);
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    word-wrap: break-word;
}

.color-type-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    padding: 8px 16px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.3);
    font-size: clamp(0.8rem, 3vw, 1rem);
    max-width: 95%;
    word-wrap: break-word;
}

.main-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
}

.controls-panel {
    background: rgba(255,255,255,0.98);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 20px 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    width: 100%;
    overflow: hidden;
}

.control-section {
    margin-bottom: 20px;
}

.control-section:last-child {
    margin-bottom: 0;
}

.control-section h3 {
    color: #2d3748;
    margin-bottom: 12px;
    font-size: clamp(1rem, 4vw, 1.2rem);
    font-weight: 600;
}

.color-type-selector {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.color-type-btn {
    padding: 10px 8px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    color: #4a5568;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: clamp(0.85rem, 3.5vw, 1rem);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.color-type-btn:hover {
    border-color: #667eea;
}

.color-type-btn.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: transparent;
}

.toggle-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.toggle-btn {
    padding: 12px 8px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-height: 70px;
}

.toggle-btn.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: transparent;
}

.toggle-btn .icon {
    font-size: clamp(1.2rem, 5vw, 1.5rem);
}

.toggle-btn .label {
    font-size: clamp(0.8rem, 3vw, 0.95rem);
}

.color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
    gap: 8px;
    max-width: 100%;
}

.color-btn {
    width: 100%;
    aspect-ratio: 1;
    min-height: 50px;
    max-height: 70px;
    border-radius: 10px;
    border: 3px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    touch-action: manipulation;
}

.color-btn:active {
    transform: scale(0.95);
}

.color-btn.active {
    border-color: #667eea;
    box-shadow: 0 0 0 2px white, 0 0 0 4px #667eea;
}

.color-btn.recommended {
    box-shadow: 0 0 0 3px gold;
}

.color-btn.recommended.active {
    box-shadow: 0 0 0 2px white, 0 0 0 4px gold;
}

.recommend-badge {
    color: gold;
    font-size: clamp(1rem, 4vw, 1.2rem);
    text-shadow: 0 0 3px rgba(0,0,0,0.8);
    filter: drop-shadow(0 0 2px rgba(255,215,0,0.8));
}

.clothing-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.option-group label {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 6px;
    display: block;
    font-size: clamp(0.85rem, 3vw, 0.95rem);
}

.option-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
}

.option-btn {
    padding: 9px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: clamp(0.8rem, 3vw, 0.9rem);
    white-space: nowrap;
}

.option-btn.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: transparent;
}

.style-tips {
    background: #f7fafc;
    border-radius: 10px;
    padding: 12px;
}

.tip {
    margin-bottom: 6px;
    color: #4a5568;
    line-height: 1.5;
    font-size: clamp(0.8rem, 3vw, 0.95rem);
    word-wrap: break-word;
}

.tip:last-child {
    margin-bottom: 0;
}

.model-section {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
}

.model-container {
    background: rgba(255,255,255,0.98);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    width: 100%;
    overflow: hidden;
}

.model-box {
    width: 100%;
    max-width: 350px;
    aspect-ratio: 1;
    margin: 0 auto;
    background: #f0f0f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.loading-overlay, .error-overlay {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    color: #667eea;
    text-align: center;
    padding: 15px;
}

.error-overlay {
    color: #e53e3e;
}

.error-overlay small {
    font-size: clamp(0.7rem, 2.5vw, 0.85rem);
}

.loading-overlay p,
.error-overlay p {
    font-size: clamp(0.85rem, 3vw, 1rem);
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e2e8f0;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.model-controls {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-top: 12px;
}

.control-btn {
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: clamp(0.8rem, 3vw, 0.9rem);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.control-btn:hover {
    border-color: #667eea;
}

.control-btn:active {
    transform: scale(0.98);
}

.color-analysis {
    background: rgba(255,255,255,0.98);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    width: 100%;
    overflow: hidden;
}

.color-analysis h4 {
    color: #2d3748;
    margin-bottom: 12px;
    font-size: clamp(1rem, 4vw, 1.1rem);
}

.analysis-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f7fafc;
    border-radius: 10px;
}

.color-swatch {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
    border-radius: 10px;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.color-info {
    flex: 1;
    min-width: 0;
}

.color-info p {
    margin-bottom: 4px;
    color: #4a5568;
    font-size: clamp(0.8rem, 3vw, 0.95rem);
    word-wrap: break-word;
}

.color-info p:last-child {
    margin-bottom: 0;
}

.good {
    color: #38a169;
    font-weight: 600;
}

.warning {
    color: #e53e3e;
    font-weight: 600;
}

/* ========== DESKTOP STYLES ========== */
@media (min-width: 1025px) {
    .outfit-selector {
        padding: 20px;
    }

    .main-content {
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .controls-panel {
        padding: 30px;
    }

    .control-section {
        margin-bottom: 30px;
    }

    .color-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .model-container {
        padding: 20px;
    }

    .color-analysis {
        padding: 20px;
    }
}

/* ========== TABLETS ========== */
@media (min-width: 769px) and (max-width: 1024px) {
    .main-content {
        grid-template-columns: 1fr;
        max-width: 700px;
        margin: 0 auto;
    }

    .model-section {
        order: -1;
    }

    .color-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* ========== MOBILE LANDSCAPE ========== */
@media (max-width: 768px) and (orientation: landscape) {
    .outfit-selector {
        padding: 10px;
    }

    .header {
        margin-bottom: 10px;
    }

    .header h1 {
        font-size: 1.3rem;
        margin-bottom: 8px;
    }

    .color-type-badge {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .main-content {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .controls-panel,
    .model-container,
    .color-analysis {
        padding: 10px;
    }

    .control-section {
        margin-bottom: 12px;
    }

    .control-section h3 {
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .color-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
    }

    .color-btn {
        min-height: 40px;
        max-height: 50px;
    }

    .toggle-btn {
        min-height: 60px;
        padding: 8px 6px;
    }

    .style-tips {
        padding: 8px;
    }

    .tip {
        font-size: 0.75rem;
        margin-bottom: 4px;
    }

    .model-box {
        max-width: 100%;
        max-height: 250px;
    }

    .analysis-card {
        padding: 8px;
        gap: 8px;
    }

    .color-swatch {
        width: 40px;
        height: 40px;
    }
}

/* ========== SMALL MOBILE (480px and below) ========== */
@media (max-width: 480px) {
    .outfit-selector {
        padding: 10px;
    }

    .header {
        margin-bottom: 15px;
        padding: 0 5px;
    }

    .main-content {
        gap: 15px;
    }

    .controls-panel {
        padding: 15px 12px;
    }

    .control-section {
        margin-bottom: 18px;
    }

    .color-type-selector {
        gap: 6px;
    }

    .color-type-btn {
        padding: 9px 6px;
    }

    .toggle-group {
        gap: 6px;
    }

    .toggle-btn {
        padding: 10px 6px;
        min-height: 65px;
    }

    .color-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .color-btn {
        min-height: 55px;
        max-height: 65px;
    }

    .clothing-options {
        gap: 10px;
    }

    .option-buttons {
        gap: 6px;
    }

    .option-btn {
        padding: 8px 8px;
    }

    .style-tips {
        padding: 10px;
    }

    .model-container {
        padding: 12px;
    }

    .model-controls {
        gap: 6px;
        margin-top: 10px;
    }

    .control-btn {
        padding: 9px 10px;
    }

    .color-analysis {
        padding: 12px;
    }

    .analysis-card {
        padding: 10px;
        gap: 10px;
    }
}

/* ========== EXTRA SMALL MOBILE (360px and below) ========== */
@media (max-width: 360px) {
    .outfit-selector {
        padding: 8px;
    }

    .header {
        margin-bottom: 12px;
    }

    .color-type-badge {
        padding: 6px 10px;
    }

    .main-content {
        gap: 12px;
    }

    .controls-panel {
        padding: 12px 10px;
        border-radius: 12px;
    }

    .control-section {
        margin-bottom: 15px;
    }

    .control-section h3 {
        margin-bottom: 10px;
    }

    .color-type-selector {
        gap: 5px;
    }

    .color-type-btn {
        padding: 8px 5px;
        border-radius: 8px;
    }

    .toggle-group {
        gap: 5px;
    }

    .toggle-btn {
        padding: 8px 5px;
        min-height: 60px;
        border-radius: 8px;
    }

    .color-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
    }

    .color-btn {
        min-height: 50px;
        max-height: 60px;
        border-radius: 8px;
    }

    .option-buttons {
        gap: 5px;
    }

    .option-btn {
        padding: 7px 6px;
        border-radius: 6px;
    }

    .style-tips {
        padding: 8px;
        border-radius: 8px;
    }

    .model-container {
        padding: 10px;
        border-radius: 12px;
    }

    .model-box {
        border-radius: 10px;
    }

    .model-controls {
        gap: 5px;
        grid-template-columns: 1fr 1fr;
    }

    .control-btn {
        padding: 8px 6px;
        font-size: 0.75rem;
    }

    .color-analysis {
        padding: 10px;
        border-radius: 12px;
    }

    .analysis-card {
        padding: 8px;
        gap: 8px;
        border-radius: 8px;
    }

    .color-swatch {
        width: 45px;
        height: 45px;
    }
}

/* ========== ОЧЕНЬ МАЛЕНЬКИЕ ЭКРАНЫ (320px) ========== */
@media (max-width: 320px) {
    .header h1 {
        font-size: 1.2rem;
    }

    .color-type-badge {
        font-size: 0.75rem;
        padding: 5px 8px;
    }

    .controls-panel {
        padding: 10px 8px;
    }

    .control-section {
        margin-bottom: 12px;
    }

    .color-type-btn {
        font-size: 0.8rem;
    }

    .toggle-btn {
        min-height: 55px;
    }

    .toggle-btn .icon {
        font-size: 1.1rem;
    }

    .toggle-btn .label {
        font-size: 0.75rem;
    }

    .color-grid {
        gap: 5px;
    }

    .color-btn {
        min-height: 45px;
        max-height: 55px;
    }

    .recommend-badge {
        font-size: 0.9rem;
    }

    .tip {
        font-size: 0.75rem;
    }

    .control-btn {
        font-size: 0.7rem;
        padding: 7px 5px;
    }
}

/* ========== FIX FOR OVERFLOW ========== */
@media (max-width: 768px) {
    * {
        -webkit-overflow-scrolling: touch;
    }

    .outfit-selector,
    .main-content,
    .controls-panel,
    .model-section,
    .model-container,
    .color-analysis {
        max-width: 100%;
        overflow-x: hidden;
    }

    button {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }
}

/* ========== БЕЗОПАСНАЯ ЗОНА ДЛЯ iOS ========== */
@supports (padding: max(0px)) {
    .outfit-selector {
        padding-left: max(10px, env(safe-area-inset-left));
        padding-right: max(10px, env(safe-area-inset-right));
        padding-bottom: max(15px, env(safe-area-inset-bottom));
    }
}
</style>
