<template>
    <div class="mx-auto text-center">
        <h1 clas="mb-3">Previous Conversions</h1>
        <div v-if="hasPastConversions" class="mt-4">
            <div v-if="pastConversions.video.length > 0">
                <PastConversionList :pastConversions="pastConversions.video" name="video" @update="handleUpdate()"/>
            </div>
            <div v-if="pastConversions.audio.length > 0">
                <PastConversionList :pastConversions="pastConversions.audio" name="audio" @update="handleUpdate()" />
            </div>
            <div v-if="pastConversions.image.length > 0">
                <PastConversionList :pastConversions="pastConversions.image" name="image" @update="handleUpdate()"/>
            </div>
            <div v-if="pastConversions.spreadsheet.length > 0">
                <PastConversionList :pastConversions="pastConversions.spreadsheet" name="spreadsheet" @update="handleUpdate()"/>
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
    created() {
        this.removeOldConversions();
    },
    methods: {
        removeOldConversions() {
            const threeDaysAgo = new Date();
            threeDaysAgo.setDate(threeDaysAgo.getDate() - 3);

            // Get past conversions from localStorage
            const pastConversions = JSON.parse(localStorage.getItem('pastConversions'));

            // Filter out conversions that are older than three days
            const recentConversions = {
                video: pastConversions.video ? pastConversions.video.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                audio: pastConversions.audio ? pastConversions.audio.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                image: pastConversions.image ? pastConversions.image.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                spreadsheet: pastConversions.spreadsheet ? pastConversions.spreadsheet.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : []
            };

            // Update localStorage with recent conversions
            localStorage.setItem('pastConversions', JSON.stringify(recentConversions));
        },
        handleUpdate() {
            console.log('udposaujfoiads');
            this.pastConversions = JSON.parse(localStorage.getItem('pastConversions'));

        }
    },
    computed: {
hasPastConversions() {
    const threeDaysAgo = new Date();
    threeDaysAgo.setDate(threeDaysAgo.getDate() - 3);

    const hasRecentConversion = (conversions) => {
        return Array.isArray(conversions) && conversions.some(conversion => 
            new Date(conversion.time) >= threeDaysAgo
        );
    };

    return hasRecentConversion(this.pastConversions) || 
        hasRecentConversion(this.pastConversions && this.pastConversions.audio) || 
        hasRecentConversion(this.pastConversions && this.pastConversions.image) || 
        hasRecentConversion(this.pastConversions && this.pastConversions.spreadsheet);
}
    },
}
</script>

<style>
    a {
        cursor: pointer;
    }
</style>