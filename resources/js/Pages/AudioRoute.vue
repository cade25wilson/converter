<template>
    <div class="mx-auto text-center">
        <h1>Audio Converter</h1>
        <ConversionForm page="audio" />
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