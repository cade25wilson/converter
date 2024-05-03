<template>
    <div class="mx-auto text-center">
        <h1 clas="mb-3">Previous Conversions</h1>
        <div v-if="hasPastConversions" class="mt-4">
            <div v-if="pastConversions.video.length > 0">
                <PastConversionList :pastConversions="pastConversions.video" name="video"/>
            </div>
            <div v-if="pastConversions.audio.length > 0">
                <PastConversionList :pastConversions="pastConversions.audio" name="audio"/>
            </div>
            <div v-if="pastConversions.image.length > 0">
                <PastConversionList :pastConversions="pastConversions.image" name="image"/>
            </div>
            <div v-if="pastConversions.spreadsheet.length > 0">
                <PastConversionList :pastConversions="pastConversions.spreadsheet" name="spreadsheet"/>
            </div>
        </div>
        <div v-else class="mt-4">
            <p>No previous conversions</p>
        </div>
    </div>   
</template>

<script>
import PastConversionList from "../components/PastConversionList.vue";

export default {
    name: 'PastRoute',
    components: {
        PastConversionList
    },
    data() {
        return {
            pastConversions: JSON.parse(localStorage.getItem('pastConversions'))
        }
    },
    computed: {
        hasPastConversions() {
            return this.pastConversions.video.length > 0 || 
                   this.pastConversions.audio.length > 0 || 
                   this.pastConversions.image.length > 0 || 
                   this.pastConversions.spreadsheet.length > 0;
        }
    },
}
</script>

<style>
    a {
        cursor: pointer;
    }
</style>