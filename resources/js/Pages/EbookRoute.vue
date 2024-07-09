<template>
    <div class="mx-auto text-center">
        <h1>Ebook Converter</h1>
        <ConversionForm page="ebook" />
        <CardsComponent />
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import ConversionForm from '../components/ConversionForm.vue';

export default {
    components: {
        ConversionForm,
        CardsComponent: defineAsyncComponent(() => import('../components/CardsComponent.vue'))        
    }
}
</script>