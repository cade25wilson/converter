<template>
    <div class="mx-auto text-center">
        <h1>Image Converter</h1>
        <ConversionForm page="image" />
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