<template>
    <div class="app">
        <!-- Header -->
        <header class="header">
            <div class="nav-container">
                <img src="/assets/logo.png" alt="ColorStyle Logo" class="logo-img"  />

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" @click="toggleMobileMenu" aria-label="Menu">
                    <span :class="{ open: mobileMenuOpen }"></span>
                    <span :class="{ open: mobileMenuOpen }"></span>
                    <span :class="{ open: mobileMenuOpen }"></span>
                </button>

                <!-- Navigation -->
                <nav :class="{ 'mobile-open': mobileMenuOpen }">
                    <ul class="nav-menu">
                        <li><a href="#home" class="nav-link" @click.prevent="scrollToSection('home')">Головна</a></li>
                        <li><a href="#about" class="nav-link" @click.prevent="scrollToSection('about')">Про нас</a></li>
                        <li><a href="#color-types" class="nav-link" @click.prevent="scrollToSection('color-types')">Кольоротипи</a></li>
                        <li><a href="#reviews" class="nav-link" @click.prevent="scrollToSection('reviews')">Відгуки</a></li>
                        <li><a href="#contact" class="nav-link" @click.prevent="scrollToSection('contact')">Контакти</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="hero">
            <div class="hero-container">
                <div class="hero-content">
                    <h1>Знайди свій ідеальний стиль</h1>
                    <p>Відкрий для себе одяг, який підкреслить твою природну красу за допомогою визначення кольоротипу. Персональний підбір від професійних стилістів.</p>
                    <button class="cta-button" @click="scrollToSection('color-types')">Дізнатися більше</button>
                </div>
                <div class="hero-image">
                    <img src="/assets/person.png" alt="Person" class="person-img" />
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="section about">
            <div class="section-container">
                <h2 class="section-title">Про нас</h2>
                <div class="about-content">
                    <p>Ми — команда професійних стилістів та колористів, які допомагають людям відкрити свій унікальний стиль. Наша місія — зробити моду доступною та зрозумілою для кожного, хто хоче виглядати гармонійно та впевнено.</p>
                    <br>
                    <p>За роки роботи ми допомогли тисячам клієнтів знайти свій ідеальний кольоротип та створити гардероб, який підкреслює їхню природну красу. Ми використовуємо найсучасніші методики та індивідуальний підхід до кожного клієнта.</p>
                </div>
            </div>
        </section>

        <!-- Color Types Section -->
        <section id="color-types" class="section color-types">
            <div class="section-container">
                <h2 class="section-title">Кольоротипи</h2>
                <div class="carousel-container">
                    <div class="carousel">
                        <div class="carousel-track" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                            <div v-for="(colorType, index) in colorTypes" :key="index"
                                 class="color-type-card"
                                 :style="{
                                    backgroundColor: colorType.bgColor,
                                    backgroundImage: `linear-gradient(135deg, ${colorType.bgColor}, ${colorType.bgColorDark})`
                                }">
                                <h3>{{ colorType.name }}</h3>
                                <p>{{ colorType.description }}</p>
                                <div class="color-palette">
                                    <div v-for="color in colorType.palette" :key="color"
                                         class="color-dot" :style="{ backgroundColor: color }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-nav">
            <span v-for="(_, index) in colorTypes" :key="index"
                  class="nav-dot" :class="{ active: currentSlide === index }"
                  @click="currentSlide = index"></span>
                    </div>
                </div>
                <div style="text-align: center;">
                    <button class="cta-button" @click.prevent="$router.push('/test')">Пройти тест на кольоротип</button>
                </div>
            </div>
        </section>

        <!-- Reviews Section -->
        <section id="reviews" class="section reviews">
            <div class="section-container">
                <h2 class="section-title">Відгуки наших клієнтів</h2>
                <div class="reviews-carousel">
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">{{ reviews[currentReview].text }}</p>
                        <div class="review-author">{{ reviews[currentReview].author }}</div>
                    </div>
                    <div class="carousel-nav" style="margin-top: 2rem;">
            <span v-for="(_, index) in reviews" :key="index"
                  class="nav-dot" :class="{ active: currentReview === index }"
                  @click="currentReview = index"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="footer">
            <div class="section-container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h4>FitHappens</h4>
                        <p>Твій персональний стиліст для ідеального образу. Відкривай красу через правильний колір.</p>
                    </div>
                    <div class="footer-section">
                        <h4>Контакти</h4>
                        <p>📧 <a href="#" @click.prevent="$router.push('/second')">info@fithappens.ua</a></p>
                        <p>📞 +38 (050) 123-45-67</p>
                        <p>📍 м. Київ, вул. Модна, 15</p>
                    </div>
                    <div class="footer-section">
                        <h4>Послуги</h4>
                        <p><a href="#">Визначення кольоротипу</a></p>
                        <p><a href="#">Персональний стайлинг</a></p>
                        <p><a href="#">Шопінг-супровід</a></p>
                        <p><a href="#">Онлайн консультації</a></p>
                    </div>
                    <div class="footer-section">
                        <h4>Соціальні мережі</h4>
                        <p><a href="#">Instagram</a></p>
                        <p><a href="#">Facebook</a></p>
                        <p><a href="#">Telegram</a></p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2024 FitHappens. Всі права захищені.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const currentSlide = ref(0)
const currentReview = ref(0)
const mobileMenuOpen = ref(false)

const colorTypes = ref([
    {
        name: 'Весна',
        description: 'Теплий та світлий кольоротип з золотистими відтінками. Підходять персикові, коралові, золотисті кольори.',
        palette: ['#FFB07A', '#FFD700', '#98FB98', '#FFA07A', '#F0E68C'],
        bgColor: '#FFB07A',
        bgColorDark: '#FF8C42'
    },
    {
        name: 'Літо',
        description: 'Холодний та м\'який кольоротип з сірими підтонами. Ідеальні пастельні та приглушені кольори.',
        palette: ['#B0C4DE', '#DDA0DD', '#F0F8FF', '#E6E6FA', '#D8BFD8'],
        bgColor: '#B0C4DE',
        bgColorDark: '#6495ED'
    },
    {
        name: 'Осінь',
        description: 'Теплий та глибокий кольоротип з золотистими підтонами. Підходять земляні, оранжеві та золотисті відтінки.',
        palette: ['#CD853F', '#DAA520', '#B22222', '#8B4513', '#FF6347'],
        bgColor: '#CD853F',
        bgColorDark: '#A0522D'
    },
    {
        name: 'Зима',
        description: 'Холодний та контрастний кольоротип. Ідеальні яскраві, чисті кольори та класичні поєднання.',
        palette: ['#000080', '#DC143C', '#FFFFFF', '#4169E1', '#8A2BE2'],
        bgColor: '#4169E1',
        bgColorDark: '#000080'
    }
])

const reviews = ref([
    {
        text: "Дякую FitHappens за неймовірну трансформацію! Тепер я точно знаю, які кольори мені підходять, і отримую компліменти щодня. Рекомендую всім!",
        author: "Олена К."
    },
    {
        text: "Вау! Просто вау! Це неймовірно. Після використання рекомендацій від FitHappens, мій гардероб став справжнім витвором мистецтва. Дякую за професіоналізм!",
        author: "Вікторія Перемога"
    },
    {
        text: "Професійний підхід та індивідуальна консультація перевершили всі очікування. Мій гардероб став набагато функціональніше та стильніше.",
        author: "Андрій М."
    },
    {
        text: "Онлайн тест був дуже зручний, а результати — точні! Тепер купую тільки ті речі, які дійсно мені підходять. Економлю час та гроші.",
        author: "Марія В."
    },
    {
        text: "Завдяки визначенню кольоротипу я нарешті знайшла свій стиль. Почуваюся впевненіше та отримую набагато більше компліментів!",
        author: "Ірина Л."
    }
])

let slideInterval
let reviewInterval

onMounted(() => {
    slideInterval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % colorTypes.value.length
    }, 5000)

    reviewInterval = setInterval(() => {
        currentReview.value = (currentReview.value + 1) % reviews.value.length
    }, 4000)

    const observeElements = document.querySelectorAll('.section')
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up')
            }
        })
    })

    observeElements.forEach(el => observer.observe(el))
})

onUnmounted(() => {
    clearInterval(slideInterval)
    clearInterval(reviewInterval)
})

function scrollToSection(elementId) {
    const element = document.getElementById(elementId)
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' })
        mobileMenuOpen.value = false
    }
}

function toggleMobileMenu() {
    mobileMenuOpen.value = !mobileMenuOpen.value
}
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: #333;
    overflow-x: hidden;
}

/* Header Styles */
.header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    padding: 1rem 0;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo-img {
    height: 50px;
    vertical-align: middle;

}

/* Mobile Menu Button */
.mobile-menu-btn {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    z-index: 1001;
}

.mobile-menu-btn span {
    width: 25px;
    height: 3px;
    background: #00356a;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.mobile-menu-btn span.open:nth-child(1) {
    transform: rotate(45deg) translate(7px, 7px);
}

.mobile-menu-btn span.open:nth-child(2) {
    opacity: 0;
}

.mobile-menu-btn span.open:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: 2rem;
    align-items: center;
}

.nav-link {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: color 0.3s ease;
    cursor: pointer;
    font-size: 1rem;
}

.nav-link:hover {
    color: #00356a;
}

/* Hero Section */
.hero {
    min-height: 100vh;
    background: linear-gradient(135deg, #00356A 0%, #3286AA 100%);
    display: flex;
    align-items: center;
    color: white;
    position: relative;
    overflow: hidden;
    padding-top: 80px;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-content h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    opacity: 0;
    animation: slideInLeft 1s ease 0.3s forwards;
}

.hero-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    animation: slideInLeft 1s ease 0.6s forwards;
    line-height: 1.8;
}

.hero-image {
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    animation: slideInRight 1s ease 0.9s forwards;
}

.person-img {
    width: 100%;
    max-width: 300px;
    height: auto;
    filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
}

/* Section Styles */
.section {
    padding: 5rem 0;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 3rem;
    background: linear-gradient(135deg, #3286aa 0%, #2f8db4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* About Section */
.about {
    background: #f8fafc;
}

.about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
    font-size: 1.1rem;
    line-height: 1.8;
    color: #666;
}

/* Color Types Section */
.color-types {
    background: white;
}

.carousel-container {
    position: relative;
    max-width: 900px;
    margin: 0 auto 3rem;
}

.carousel {
    overflow: hidden;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.carousel-track {
    display: flex;
    transition: transform 0.5s ease;
}

.color-type-card {
    min-width: 100%;
    padding: 3rem 2rem;
    text-align: center;
    color: white;
}

.color-type-card h3 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.color-type-card p {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 2rem;
    line-height: 1.6;
}

.color-palette {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.color-dot {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.carousel-nav {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.nav-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ddd;
    cursor: pointer;
    transition: all 0.3s ease;
}

.nav-dot.active {
    background: #00356a;
    transform: scale(1.2);
}

.cta-button {
    display: inline-block;
    padding: 1rem 2.5rem;
    background: linear-gradient(135deg, #5f7999 0%, #3286aa 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    border: none;
    cursor: pointer;
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

/* Reviews Section */
.reviews {
    background: #f8fafc;
}

.reviews-carousel {
    max-width: 800px;
    margin: 0 auto;
}

.review-card {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin: 0 1rem;
}

.review-text {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    font-style: italic;
    color: #555;
}

.review-author {
    font-weight: 600;
    color: #333;
}

.stars {
    color: #ffd700;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

/* Footer */
.footer {
    background: #1a202c;
    color: white;
    padding: 3rem 0 1rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h4 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #667eea;
}

.footer-section p,
.footer-section a {
    color: #a0aec0;
    text-decoration: none;
    line-height: 1.8;
    display: block;
    margin-bottom: 0.5rem;
}

.footer-section a:hover {
    color: white;
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #2d3748;
    color: #a0aec0;
}

/* Animations */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.8s ease forwards;
}

/* ========== RESPONSIVE DESIGN ========== */

/* Tablets (768px and below) */
@media (max-width: 768px) {
    .mobile-menu-btn {
        display: flex;
    }

    nav {
        position: fixed;
        top: 0;
        right: -100%;
        width: 70%;
        height: 100vh;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        transition: right 0.3s ease;
        padding-top: 80px;
        box-shadow: -5px 0 20px rgba(0,0,0,0.1);
    }

    nav.mobile-open {
        right: 0;
    }

    .nav-menu {
        flex-direction: column;
        gap: 0;
        padding: 2rem;
    }

    .nav-menu li {
        width: 100%;
        border-bottom: 1px solid #e2e8f0;
    }

    .nav-link {
        display: block;
        padding: 1rem 0;
        font-size: 1.1rem;
    }

    .hero-container {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 2rem;
        padding-top: 2rem;
    }

    .hero-content h1 {
        font-size: 2.2rem;
    }

    .hero-content p {
        font-size: 1rem;
    }

    .hero-image {
        order: -1;
    }

    .person-img {
        max-width: 250px;
    }

    .section {
        padding: 3rem 0;
    }

    .section-title {
        font-size: 2rem;
    }

    .about-content {
        font-size: 1rem;
    }

    .color-type-card {
        padding: 2rem 1.5rem;
    }

    .color-type-card h3 {
        font-size: 1.6rem;
    }

    .color-type-card p {
        font-size: 1rem;
    }

    .color-dot {
        width: 35px;
        height: 35px;
    }

    .review-card {
        padding: 1.5rem;
        margin: 0 0.5rem;
    }

    .review-text {
        font-size: 1rem;
    }

    .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .cta-button {
        padding: 0.9rem 2rem;
        font-size: 1rem;
    }
}

/* Mobile (480px and below) */
@media (max-width: 480px) {
    .logo-img {
        height: 40px;
    }

    nav {
        width: 85%;
    }

    .hero {
        min-height: auto;
        padding-top: 70px;
        padding-bottom: 2rem;
    }

    .hero-content h1 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .hero-content p {
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }

    .person-img {
        max-width: 200px;
    }

    .section {
        padding: 2.5rem 0;
    }

    .section-container {
        padding: 0 1rem;
    }

    .section-title {
        font-size: 1.6rem;
        margin-bottom: 2rem;
    }

    .about-content {
        font-size: 0.95rem;
    }

    .color-type-card {
        padding: 1.5rem 1rem;
    }

    .color-type-card h3 {
        font-size: 1.4rem;
    }

    .color-type-card p {
        font-size: 0.95rem;
    }

    .color-palette {
        gap: 0.7rem;
    }

    .color-dot {
        width: 30px;
        height: 30px;
    }

    .review-card {
        padding: 1.2rem;
    }

    .review-text {
        font-size: 0.95rem;
    }

    .stars {
        font-size: 1rem;
    }

    .footer-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .footer-section h4 {
        font-size: 1.1rem;
    }

    .footer-section p,
    .footer-section a {
        font-size: 0.9rem;
    }

    .cta-button {
        padding: 0.8rem 1.5rem;
        font-size: 0.95rem;
        width: 100%;
    }
}

/* Extra small devices (360px and below) */
@media (max-width: 360px) {
    .hero-content h1 {
        font-size: 1.5rem;
    }

    .section-title {
        font-size: 1.4rem;
    }

    .color-type-card h3 {
        font-size: 1.2rem;
    }
}
</style>
