<template>
    <div class="mx-auto text-center">
        <h1>Video Converter</h1>
        <ConversionForm page="video" />
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