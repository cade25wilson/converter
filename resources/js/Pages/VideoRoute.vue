<template>
    <div class="mx-auto text-center">
        <h1>Video Converter</h1>
        <ConversionForm page="video" />
        <CardsComponent />
    </div>
</template>

<script>
import ConversionForm from '../components/ConversionForm.vue';
import CardsComponent from '../components/CardsComponent.vue';

export default {
    components: {
        ConversionForm,
        CardsComponent
    }
}
</script>