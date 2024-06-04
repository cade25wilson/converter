<template>
    <div class="mx-auto text-center">
        <h1 clas="mb-3">Previous Conversions</h1>
        <div v-if="hasPastConversions" class="mt-4">
            <div v-if="pastConversions.archive.length > 0">
                <PastConversionList :pastConversions="pastConversions.archive" name="archive" @update="handleUpdate()" @email="handleEmail"/>
            </div>
            <div v-if="pastConversions.audio.length > 0">
                <PastConversionList :pastConversions="pastConversions.audio" name="audio" @update="handleUpdate()" @email="handleEmail"/>
            </div>
            <div v-if="pastConversions.ebook.length > 0">
                <PastConversionList :pastConversions="pastConversions.ebook" name="ebook" @update="handleUpdate()" @email="handleEmail"/>
            </div>
            <div v-if="pastConversions.image.length > 0">
                <PastConversionList :pastConversions="pastConversions.image" name="image" @update="handleUpdate()" @email="handleEmail"/>
            </div>
            <div v-if="pastConversions.spreadsheet.length > 0">
                <PastConversionList :pastConversions="pastConversions.spreadsheet" name="spreadsheet" @update="handleUpdate()" @email="handleEmail"/>
            </div>
            <div v-if="pastConversions.video.length > 0">
                <PastConversionList :pastConversions="pastConversions.video" name="video" @update="handleUpdate()" @email="handleEmail"/>
            </div>
            <EmailFiles :type="emailType"/>
        </div>
        <div v-else class="mt-4 noconversions">
            <p>No previous conversions</p>
        </div>
    </div>   
</template>

<script>
import EmailFiles from "../components/EmailFiles.vue";
import PastConversionList from "../components/PastConversionList.vue";
import * as bootstrap from 'bootstrap'

export default {
    name: 'PastRoute',
    components: {
        PastConversionList,
        EmailFiles
    },
    data() {
        return {
            pastConversions: JSON.parse(localStorage.getItem('pastConversions')),
            emailType: ''
        }
    },
    created() {
        this.removeOldConversions();
    },
    methods: {
        handleEmail(type) {
            console.log(type);
            this.emailType = type;
            var myModal = new bootstrap.Modal(document.getElementById('myModal'), {
                keyboard: false
            });
            myModal.show();
        },
        removeOldConversions() {
            const threeDaysAgo = new Date();
            threeDaysAgo.setDate(threeDaysAgo.getDate() - 3);

            // Get past conversions from localStorage
            const pastConversions = JSON.parse(localStorage.getItem('pastConversions'));

            // Filter out conversions that are older than three days
            const recentConversions = {
                archive: pastConversions.archive ? pastConversions.archive.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                audio: pastConversions.audio ? pastConversions.audio.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                ebook: pastConversions.ebook ? pastConversions.ebook.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                image: pastConversions.image ? pastConversions.image.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                spreadsheet: pastConversions.spreadsheet ? pastConversions.spreadsheet.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
                video: pastConversions.video ? pastConversions.video.filter(conversion => new Date(conversion.time) >= threeDaysAgo) : [],
            };

            // Update localStorage with recent conversions
            localStorage.setItem('pastConversions', JSON.stringify(recentConversions));
        },
        handleUpdate() {
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
                hasRecentConversion(this.pastConversions && this.pastConversions.archive) ||
                hasRecentConversion(this.pastConversions && this.pastConversions.audio) || 
                hasRecentConversion(this.pastConversions && this.pastConversions.ebook) ||
                hasRecentConversion(this.pastConversions && this.pastConversions.image) || 
                hasRecentConversion(this.pastConversions && this.pastConversions.spreadsheet)||
                hasRecentConversion(this.pastConversions && this.pastConversions.video);
        }
    },
}
</script>