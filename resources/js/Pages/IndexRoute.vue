<template>
    <div class="mx-auto text-center">
        <h1>Index</h1>
        <ConvertFile />
        <router-link to="/test">Take me to the test page</router-link>
    </div>
</template>

<script>
import ConvertFile from '../components/ConvertFile.vue';

export default {
    components: {
        ConvertFile
    }
}
</script>