<template>
    <div class="color-type-wrapper">
        <div class="color-type">
            <h2>Визначення кольоротипу</h2>

            <div class="form-group">
                <label>
                    Який у вас колір очей?
                    <input v-model="eyes" placeholder="Наприклад: карі" />
                </label>

                <label>
                    Колір волосся?
                    <input v-model="hair" placeholder="Наприклад: русяве" />
                </label>

                <label>
                    Відтінок шкіри?
                    <input v-model="skin" placeholder="Наприклад: світла" />
                </label>
            </div>

            <button @click="getColorType" :disabled="loading || !isFormValid" class="submit-btn">
                {{ loading ? "Визначаємо..." : "Отримати кольоротип" }}
            </button>

            <!-- Показуємо помилку, якщо є -->
            <div v-if="error" class="error">
                <p>❌ {{ error }}</p>
            </div>

            <div v-if="result" class="result">
                <h3>Ваш кольоротип:</h3>
                <p class="result-text">{{ result }}</p>
                <button class="next-btn" @click="goToExamples">
                    Перейти до примірок
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const eyes = ref("");
const hair = ref("");
const skin = ref("");
const result = ref("");
const loading = ref(false);
const error = ref("");

const isFormValid = computed(() => {
    return eyes.value.trim() && hair.value.trim() && skin.value.trim();
});

async function getColorType() {
    loading.value = true;
    result.value = "";
    error.value = "";

    try {
        console.log('🔄 Відправка запиту до Laravel API...');

        const response = await axios.post('/api/v1/color-type/determine', {
            eyes: eyes.value,
            hair: hair.value,
            skin: skin.value
        });

        console.log('✅ Відповідь API:', response.data);

        if (response.data.success) {
            result.value = response.data.colorType;
            localStorage.setItem("result", response.data.colorType);
        } else {
            error.value = response.data.message || "Помилка при визначенні кольоротипу";
        }

    } catch (err) {
        console.error("❌ Помилка запиту:", err);

        if (err.response) {
            if (err.response.data.errors) {
                const validationErrors = Object.values(err.response.data.errors).flat();
                error.value = validationErrors.join(', ');
            } else {
                error.value = err.response.data.message || "Помилка сервера";
            }
        } else if (err.request) {
            error.value = "Немає відповіді від сервера. Перевірте чи запущений Laravel (php artisan serve)";
        } else {
            error.value = `Помилка: ${err.message}`;
        }
    } finally {
        loading.value = false;
    }
}

function goToExamples() {
    if (result.value) {
        router.push("/second");
    }
}
</script>

<style scoped>
.color-type-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #00356a, #3286aa);
    padding: 20px;
}

.color-type {
    max-width: 450px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 20px;
    background: rgba(255, 255, 255, 0.98);
    padding: 32px;
    border-radius: 16px;
    color: #00356a;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

h2 {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 10px 0;
    color: #00356a;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

label {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-weight: 600;
    font-size: 1rem;
    color: #00356a;
}

input {
    padding: 12px 14px;
    border-radius: 10px;
    border: 2px solid #91bbd1;
    background: #f9f9f9;
    color: #00356a;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #3286aa;
    box-shadow: 0 0 0 3px rgba(50, 134, 170, 0.15);
    background: white;
}

input::placeholder {
    color: #91bbd1;
}

.submit-btn {
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #3286aa, #00356a);
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.05rem;
    box-shadow: 0 4px 15px rgba(0, 53, 106, 0.3);
}

.submit-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #00356a, #001f3f);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 53, 106, 0.4);
}

.submit-btn:disabled {
    background: linear-gradient(135deg, #aaa, #888);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.error {
    padding: 14px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ff4444, #cc0000);
    color: white;
    font-weight: 600;
    animation: shake 0.5s;
    box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
}

.error p {
    margin: 0;
    font-size: 0.95rem;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.result {
    margin-top: 10px;
    padding: 20px;
    border-radius: 12px;
    background: linear-gradient(135deg, #91bbd1, #6a9fb8);
    color: white;
    animation: fadeIn 0.5s;
    box-shadow: 0 4px 20px rgba(0, 53, 106, 0.2);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result h3 {
    margin: 0 0 12px 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.result-text {
    margin: 0 0 16px 0;
    font-size: 2rem;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.next-btn {
    margin-top: 8px;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: white;
    color: #00356a;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    width: 100%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
}

.next-btn:hover {
    background: #f0f8ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* ========== RESPONSIVE DESIGN ========== */

/* Tablets (768px and below) */
@media (max-width: 768px) {
    .color-type-wrapper {
        padding: 15px;
    }

    .color-type {
        max-width: 100%;
        padding: 28px 24px;
        gap: 18px;
    }

    h2 {
        font-size: 1.8rem;
    }

    label {
        font-size: 0.95rem;
    }

    input {
        padding: 11px 12px;
        font-size: 0.95rem;
    }

    .submit-btn {
        padding: 13px;
        font-size: 1rem;
    }

    .result h3 {
        font-size: 1.2rem;
    }

    .result-text {
        font-size: 1.8rem;
    }
}

/* Mobile (480px and below) */
@media (max-width: 480px) {
    .color-type-wrapper {
        padding: 10px;
        align-items: flex-start;
        padding-top: 30px;
    }

    .color-type {
        padding: 24px 20px;
        gap: 16px;
        border-radius: 14px;
    }

    h2 {
        font-size: 1.6rem;
        margin-bottom: 5px;
    }

    .form-group {
        gap: 14px;
    }

    label {
        font-size: 0.9rem;
        gap: 5px;
    }

    input {
        padding: 10px 12px;
        font-size: 0.9rem;
        border-radius: 8px;
    }

    .submit-btn {
        padding: 12px;
        font-size: 0.95rem;
        border-radius: 8px;
    }

    .error {
        padding: 12px;
        border-radius: 8px;
    }

    .error p {
        font-size: 0.9rem;
    }

    .result {
        padding: 18px;
        border-radius: 10px;
    }

    .result h3 {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .result-text {
        font-size: 1.6rem;
        margin-bottom: 14px;
    }

    .next-btn {
        padding: 11px;
        font-size: 0.95rem;
        border-radius: 8px;
    }
}

/* Extra small devices (360px and below) */
@media (max-width: 360px) {
    .color-type {
        padding: 20px 16px;
        gap: 14px;
    }

    h2 {
        font-size: 1.4rem;
    }

    .form-group {
        gap: 12px;
    }

    label {
        font-size: 0.85rem;
    }

    input {
        padding: 9px 10px;
        font-size: 0.85rem;
    }

    .submit-btn {
        padding: 11px;
        font-size: 0.9rem;
    }

    .error p {
        font-size: 0.85rem;
    }

    .result {
        padding: 16px;
    }

    .result h3 {
        font-size: 1rem;
    }

    .result-text {
        font-size: 1.4rem;
    }

    .next-btn {
        padding: 10px;
        font-size: 0.9rem;
    }
}

/* Landscape mode for mobile */
@media (max-height: 600px) and (orientation: landscape) {
    .color-type-wrapper {
        padding: 10px;
        align-items: flex-start;
    }

    .color-type {
        padding: 20px;
        gap: 12px;
        margin: 10px 0;
    }

    h2 {
        font-size: 1.4rem;
        margin-bottom: 0;
    }

    .form-group {
        gap: 10px;
    }

    label {
        font-size: 0.85rem;
        gap: 4px;
    }

    input {
        padding: 8px 10px;
        font-size: 0.85rem;
    }

    .submit-btn {
        padding: 10px;
        font-size: 0.9rem;
    }

    .result {
        padding: 14px;
    }

    .result h3 {
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .result-text {
        font-size: 1.3rem;
        margin-bottom: 10px;
    }

    .next-btn {
        padding: 9px;
        font-size: 0.85rem;
    }
}
</style>
