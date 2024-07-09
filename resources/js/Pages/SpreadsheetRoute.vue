<template>
    <div class="mx-auto text-center">
        <h1>Spreadsheet Converter</h1>
        <ConversionForm page="spreadsheet" />
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