<template>
    <div v-if="show" class="loading-overlay">
        <div class="loader"></div>
    </div>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    }
});
</script>

<style scoped>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
}

.loader {
    width: 48px;
    height: 48px;
    display: inline-block;
    position: relative;
    border-width: 3px 2px 3px 2px;
    border-style: solid dotted solid dotted;
    border-color: #de3500 rgba(255, 255, 255, 0.3) #fff rgba(151, 107, 93, 0.3);
    border-radius: 50%;
    box-sizing: border-box;
    animation: 1s rotate linear infinite;
}

.loader:before,
.loader:after {
    content: "";
    top: 0;
    left: 0;
    position: absolute;
    border: 10px solid transparent;
    border-bottom-color: #fff;
    transform: translate(-10px, 19px) rotate(-35deg);
}

.loader:after {
    border-color: #de3500 #0000 #0000 #0000;
    transform: translate(32px, 3px) rotate(-35deg);
}

@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}
</style>
