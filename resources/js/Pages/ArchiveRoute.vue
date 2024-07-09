<template>
    <div class="mx-auto text-center">
        <h1>Archive Converter</h1>
        <ConversionForm page="archive" />
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