<template>
    <div class="mx-auto text-center">
        <h1>Spreadsheet Converter</h1>
        <ConversionForm page="spreadsheet" />
    </div>
</template>

<script>
import ConversionForm from '../components/ConversionForm.vue';

export default {
    components: {
        ConversionForm
    }
}
</script>