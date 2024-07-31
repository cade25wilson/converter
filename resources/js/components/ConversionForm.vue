<template>
    <div v-if="!isConverting" class="p-3">
        <div class="row">
            <div class="col-4 col-lg-4 col-sm-5 col-6 offset-lg-2 offset-sm-0">
                <div id="dropzone" @dragover.prevent @drop="dropFiles" v-if="files.length === 0">
                    <label class="custum-file-upload mr-3" @click="triggerFileSelection; $event.stopPropagation()">
                        <div class="icon">
                            <svg viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                                        fill="">
                                    </path>
                                </g>
                            </svg>
                        </div>
                        <div class="text" @mouseleave="showList = false">
                            <span class="text-default">Click to upload file</span>
                            <span class="arrow-down" @mouseover="showList = true">&#x25BC;</span>
                            <ul class="dropdown-list" v-show="showList">
                                <li @click.prevent="dropboxIconClicked($event)">
                                    <DropboxSvg />Dropbox
                                </li>
                                <li @click.prevent="addFileUrl($event); $event.stopPropagation()">Download from url</li>
                            </ul>
                        </div>
                        <input type="file" ref="fileInput" @change="addFiles($event)" multiple style="display: none;">
                    </label>
                </div>
                <div style="height: 300px; overflow-y: auto;" v-else>
                    <table>
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th></th>
                                <!-- Add more columns as needed -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(file, index) in files" :key="index">
                                <!-- {{file}} -->
                                <td>{{ file.name }}</td>
                                <td class="ms-2">
                                    <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"
                                        @click="removeFile(index)" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink">

                                        <title>cancel</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs>
                                            <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="linearGradient-1">
                                                <stop stop-color="#FC4343" offset="0%">

                                                </stop>
                                                <stop stop-color="#F82020" offset="100%">

                                                </stop>
                                            </linearGradient>
                                        </defs>
                                        <g id="icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g id="ui-gambling-website-lined-icnos-casinoshunter"
                                                transform="translate(-868.000000, -1910.000000)"
                                                fill="url(#linearGradient-1)" fill-rule="nonzero">
                                                <g id="4" transform="translate(50.000000, 1871.000000)">
                                                    <path
                                                        d="M821.426657,39.5856848 L830.000001,48.1592624 L838.573343,39.5856848 C839.288374,38.8706535 840.421422,38.8040611 841.267835,39.4653242 L841.414315,39.5987208 C842.195228,40.3796338 842.195228,41.645744 841.414306,42.4266667 L832.840738,51 L841.414315,59.5733429 C842.129347,60.2883742 842.195939,61.4214224 841.534676,62.2678347 L841.401279,62.4143152 C840.620366,63.1952283 839.354256,63.1952283 838.573333,62.4143055 L830.000001,53.8407376 L821.426657,62.4143152 C820.711626,63.1293465 819.578578,63.1959389 818.732165,62.5346758 L818.585685,62.4012792 C817.804772,61.6203662 817.804772,60.354256 818.585694,59.5733333 L827.159262,51 L818.585685,42.4266571 C817.870653,41.7116258 817.804061,40.5785776 818.465324,39.7321653 L818.598721,39.5856848 C819.379634,38.8047717 820.645744,38.8047717 821.426657,39.5856848 Z M820.028674,61.999873 C820.023346,61.9999577 820.018018,62 820.012689,62 Z M820.161408,61.9889406 L820.117602,61.9945129 L820.117602,61.9945129 C820.132128,61.9929912 820.146788,61.9911282 820.161408,61.9889406 Z M819.865274,61.9891349 L819.883098,61.9916147 C819.877051,61.9908286 819.87101,61.9899872 819.864975,61.9890905 L819.865274,61.9891349 Z M819.739652,61.9621771 L819.755271,61.9664589 C819.749879,61.9650278 819.744498,61.9635509 819.739126,61.9620283 L819.739652,61.9621771 Z M820.288411,61.9614133 L820.234515,61.9752112 L820.234515,61.9752112 C820.252527,61.971132 820.270527,61.9665268 820.288411,61.9614133 Z M820.401572,61.921544 L820.359957,61.9380009 L820.359957,61.9380009 C820.373809,61.9328834 820.387743,61.9273763 820.401572,61.921544 Z M819.623655,61.9214803 C819.628579,61.923546 819.626191,61.9225499 819.623806,61.921544 L819.623655,61.9214803 Z M819.506361,61.8625673 L819.400002,61.7903682 C819.444408,61.8248958 819.491056,61.8551582 819.539393,61.8811554 L819.506361,61.8625673 L819.506361,61.8625673 Z M820.51858,61.8628242 L820.486378,61.8809439 L820.486378,61.8809439 C820.496939,61.8752641 820.507806,61.8691536 820.51858,61.8628242 Z M840.881155,61.4606074 L840.862567,61.4936392 L840.862567,61.4936392 L840.790368,61.5999978 C840.824896,61.555592 840.855158,61.5089438 840.881155,61.4606074 Z M840.936494,61.3386283 L840.92148,61.3763453 L840.92148,61.3763453 C840.926791,61.3637541 840.931774,61.3512293 840.936494,61.3386283 Z M840.974777,61.2110466 L840.962177,61.2603479 L840.962177,61.2603479 C840.966711,61.2443555 840.97096,61.2277405 840.974777,61.2110466 Z M840.994445,61.0928727 L840.989135,61.1347261 L840.989135,61.1347261 C840.991174,61.1210064 840.992958,61.1069523 840.994445,61.0928727 Z M839.987311,40.9996529 L830,50.9872374 L820.012689,40.9996529 L819.999653,41.0126889 L829.987237,51 L819.999653,60.9873111 L820.012689,61.0003471 L830,51.0127626 L839.987311,61.0003471 L840.000347,60.9873111 L830.012763,51 L840.000347,41.0126889 L839.987311,40.9996529 Z M840.999873,60.9713258 L840.999916,61.0003193 L840.999916,61.0003193 C841.000041,60.9907089 841.000027,60.9810165 840.999873,60.9713258 Z M840.988941,60.8385918 L840.994513,60.8823981 L840.994513,60.8823981 C840.992991,60.8678719 840.991128,60.8532122 840.988941,60.8385918 Z M840.961413,60.7115886 L840.975211,60.7654853 L840.975211,60.7654853 C840.971132,60.7474727 840.966527,60.7294733 840.961413,60.7115886 Z M840.921544,60.5984278 L840.938001,60.6400431 L840.938001,60.6400431 C840.932883,60.6261908 840.927376,60.612257 840.921544,60.5984278 Z M840.862824,60.4814199 L840.880944,60.5136217 L840.880944,60.5136217 C840.875264,60.503061 840.869154,60.4921939 840.862824,60.4814199 Z M819.119056,41.4863783 L819.134164,41.5134185 C819.128903,41.5043379 819.123796,41.4951922 819.118845,41.4859852 L819.119056,41.4863783 Z M819.061999,41.3599569 L819.075467,41.3944079 C819.070734,41.3829341 819.066223,41.3713901 819.061935,41.3597825 L819.061999,41.3599569 Z M819.024789,41.2345147 L819.033541,41.2701072 C819.030397,41.2582611 819.027473,41.2463686 819.024771,41.234436 L819.024789,41.2345147 Z M819.005077,41.1136164 L819.008385,41.1422797 C819.007138,41.1326872 819.00603,41.12308 819.005061,41.1134615 L819.005077,41.1136164 Z M819.000419,40.9836733 L819,41.0126889 C819,41.002956 819.000141,40.993223 819.000424,40.9834934 L819.000419,40.9836733 Z M819.010865,40.8652739 L819.008385,40.8830981 C819.009171,40.8770511 819.010013,40.8710099 819.010909,40.8649753 L819.010865,40.8652739 Z M819.037823,40.7396521 L819.033541,40.7552707 C819.034972,40.7498794 819.036449,40.7444978 819.037972,40.7391264 L819.037823,40.7396521 Z M819.07852,40.6236547 C819.076454,40.6285788 819.07745,40.6261907 819.078456,40.6238057 L819.07852,40.6236547 Z M819.137433,40.5063608 L819.209632,40.4000022 C819.175104,40.444408 819.144842,40.4910562 819.118845,40.5393926 L819.137433,40.5063608 L819.137433,40.5063608 Z M820.485985,40.1188446 L820.519017,40.1374327 L820.519017,40.1374327 L820.625376,40.2096318 C820.58097,40.1751042 820.534322,40.1448418 820.485985,40.1188446 Z M839.513622,40.1190561 L839.486582,40.1341644 C839.495662,40.128903 839.504808,40.1237964 839.514015,40.1188446 L839.513622,40.1190561 Z M819.539,40.1190561 L819.511959,40.1341644 C819.52104,40.128903 819.530186,40.1237964 819.539393,40.1188446 L819.539,40.1190561 Z M840.460607,40.1188446 L840.493639,40.1374327 L840.493639,40.1374327 L840.599998,40.2096318 C840.555592,40.1751042 840.508944,40.1448418 840.460607,40.1188446 Z M819.661418,40.0634885 L819.63097,40.0754675 C819.641051,40.0713084 819.651187,40.0673212 819.661372,40.0635059 L819.661418,40.0634885 Z M820.359783,40.0619346 L820.401723,40.0785197 L820.401723,40.0785197 C820.387743,40.0726237 820.373809,40.0671166 820.359783,40.0619346 Z M839.640043,40.0619991 L839.605592,40.0754675 C839.617066,40.0707338 839.62861,40.0662229 839.640217,40.0619346 L839.640043,40.0619991 Z M840.338628,40.0635059 L840.376345,40.0785197 L840.376345,40.0785197 C840.363754,40.0732095 840.351229,40.0682261 840.338628,40.0635059 Z M819.789259,40.0251536 L819.755271,40.0335411 C819.766459,40.0305713 819.777688,40.0277987 819.788953,40.0252234 L819.789259,40.0251536 Z M820.234436,40.0247709 L820.288548,40.0386257 L820.288548,40.0386257 C820.270527,40.0334732 820.252527,40.028868 820.234436,40.0247709 Z M839.765485,40.0247888 L839.729893,40.0335411 C839.741739,40.0303966 839.753631,40.0274732 839.765564,40.0247709 L839.765485,40.0247888 Z M840.211047,40.0252234 L840.260348,40.0378229 L840.260348,40.0378229 C840.244356,40.0332892 840.227741,40.0290398 840.211047,40.0252234 Z M819.911404,40.0051132 L819.883098,40.0083853 C819.892432,40.0071719 819.901779,40.0060902 819.911137,40.0051402 L819.911404,40.0051132 Z M820.113462,40.0050614 L820.161342,40.0110494 L820.161342,40.0110494 C820.145468,40.0086743 820.12948,40.006675 820.113462,40.0050614 Z M839.886384,40.005077 L839.85772,40.0083853 C839.867313,40.0071382 839.87692,40.0060303 839.886538,40.0050614 L839.886384,40.005077 Z M840.088863,40.0051402 L840.134726,40.0108651 L840.134726,40.0108651 C840.119676,40.0086288 840.104284,40.0067057 840.088863,40.0051402 Z M839.95834,40.0004173 L840.016507,40.0004238 C839.997122,39.9998609 839.977725,39.9998588 839.95834,40.0004173 Z M819.983493,40.0004238 L820.04166,40.0004173 C820.022275,39.9998588 820.002878,39.9998609 819.983493,40.0004238 Z"
                                                        id="cancel">

                                                    </path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-5 offset-1 offset-lg-1 offset-sm-0">
                <div class="d-flex align-items-center mb-3">
                    <label for="format" class="me-3">Format</label>
                    <button class="button" @click.stop="showFormats = !showFormats">
                        <div class="button-overlay"></div>
                        <input type="hidden" v-model="selectedFormat" name="format">
                        <span>{{ selectedFormatName }}</span>
                    </button>
                </div>
                <div class="card position-absolute cardholder" v-if="showFormats" v-click-outside="hideFormats">
                    <div class="row">
                        <div v-for="format in formats" :key="format.id" class="card-body col-4">
                            <button class="button p-2"
                                @click.stop="selectedFormat = format.id, selectedFormatName = format.name, showFormats = false">{{
                                    format.name }}</button>
                        </div>
                    </div>
                </div>
                <div v-if="this.page === 'image' || this.page === 'video' || this.page === 'audio'">
                    <div class="spinner-container">
                        <label class="advanced-features">Advanced Features</label>
                        <div class="spinner-wrapper" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <div class="spinner"></div>
                        </div>
                    </div>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Advanced Features</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="modal-title">Sizing</h5>
                                <hr>
                                <div class="row my-3" v-if="this.page != 'audio'">
                                    <div class="col-4">
                                        <label class="label">Width(px)</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="number" class="input" v-model="width">
                                    </div>
                                </div>
                                <div class="row" v-if="this.page != 'audio'">
                                    <div class="col-4">
                                        <label class="label">Height(px)</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="number" class="input" v-model="height">
                                    </div>
                                </div>
                                <div class="row my-3 align-items-center" v-if="this.page === 'image'">
                                    <div class="col-4">
                                        <label class="label">Quality (%)</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="range" class="input-range" min="1" max="100" v-model="quality">
                                    </div>
                                    <div class="col-2">
                                        <input type="number" class="input" min="1" max="100" v-model="quality">
                                    </div>
                                </div>
                                <div class="row my-3" v-if="this.page === 'image'">
                                    <div class="col-4">
                                        <label class="label">Strip Metadata</label>
                                    </div>
                                    <div class="col-8">
                                        <label class="checkBox">
                                            <input id="ch1" type="checkbox" @change="stripMetadata = !stripMetadata">
                                            <div class="transition"></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="row my-3" v-if="this.page === 'video'">
                                    <div class="col-4">
                                        <label class="label">Frame Rate</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="number" class="input" v-model="frame_rate" min="1" max="300" placeholder="Frames per second">
                                    </div>
                                </div>
                                <div class="row" v-if="this.page === 'video'">
                                    <div class="col-4">
                                        <label class="label">Rotate</label>
                                    </div>
                                    <div class="col-8">
                                        <select class="input" v-model="rotate">
                                            <option value="0">None</option>
                                            <option value="90">90 degrees</option>
                                            <option value="180">180 degrees</option>
                                            <option value="270">270 degrees</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row my-3" v-if="this.page === 'video'">
                                    <div class="col-4">
                                        <label class="label">Flip</label>
                                    </div>
                                    <div class="col-8">
                                        <select class="input" v-model="flip">
                                            <option></option>
                                            <option value="h">Horizontal</option>
                                            <option value="v">Vertical</option>
                                            <option value="b">Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row my-3 align-items-center" v-if="this.page === 'video' || this.page === 'audio'">
                                    <div class="col-4">
                                        <label class="label">Audio (%)</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="range" class="input-range" min="1" max="300" v-model="audio">
                                    </div>
                                    <div class="col-2">
                                        <input type="number" class="input" min="1" max="300" v-model="audio">
                                    </div>
                                </div>
                                <!-- <div class="row my-3" v-if="this.page === 'video' || this.page === 'audio'">
                                    <div class="col-4">
                                        <label class="label">Fade In Audio</label>
                                    </div>
                                    <div class="col-8">
                                        <label class="checkBox">
                                            <input id="ch1" type="checkbox" @change="fadeIn = !fadeIn">
                                            <div class="transition"></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="row my-3" v-if="this.page === 'video' || this.page === 'audio'">
                                    <div class="col-4">
                                        <label class="label">Fade Out Audio</label>
                                    </div>
                                    <div class="col-8">
                                        <label class="checkBox">
                                            <input id="ch1" type="checkbox" @change="fadeOut = !fadeOut">
                                            <div class="transition"></div>
                                        </label>
                                    </div>
                                </div> -->
                                <div class="row my-3" v-if="this.page === 'video' || this.page === 'audio'">
    <div class="col-4">
        <label class="label">Fade In Audio</label>
    </div>
    <div class="col-8">
        <label class="checkBox">
            <input
                id="ch1"
                type="checkbox"
                :checked="fadeIn === 1"
                @change="fadeIn = $event.target.checked ? 1 : 0"
            >
            <div class="transition"></div>
        </label>
    </div>
</div>

<div class="row my-3" v-if="this.page === 'video' || this.page === 'audio'">
    <div class="col-4">
        <label class="label">Fade Out Audio</label>
    </div>
    <div class="col-8">
        <label class="checkBox">
            <input
                id="ch2" 
                type="checkbox"
                :checked="fadeOut === 1"
                @change="fadeOut = $event.target.checked ? 1 : 0"
            >
            <div class="transition"></div>
        </label>
    </div>
</div>

                                <div class="row my-3" v-if="this.page === 'audio'">
                                    <div class="col-4">
                                        <label class="label">Reverse Audio</label>
                                    </div>
                                    <div class="col-8">
                                        <label class="checkBox">
                                            <!-- <input id="ch1" type="checkbox" @change="reverseAudio = !reverseAudio"> -->
                                            <input
                                                id="ch1"
                                                type="checkbox"
                                                :checked="reverseAudio === 1"
                                                @change="reverseAudio = $event.target.checked ? 1 : 0"
                                            >
                                            <div class="transition"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="button" class="button" data-bs-dismiss="modal">Apply changes</button> -->
                                <button class="button" data-bs-dismiss="modal">
                                    <div class="button-overlay"></div>
                                    <span>Apply changes</span>
                                </button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <button class="button" @click="convertFile" :disabled="files.length === 0">
            <div class="button-overlay"></div>
            <span>Convert</span>
        </button>
    </div>
    <div class="my-3" v-else>
        <div style="display: flex; align-items: center; justify-content: center;">
            <div class="spinner-border mb-3" :class="spinnerClass" role="status"></div>
            <span class="sr-only">{{ conversionStatus.charAt(0).toUpperCase() + conversionStatus.slice(1) }}</span>
        </div>
        <button class="btn btn-primary" @click="resetConversion()">Convert Another</button>
    </div>
    {{fadeIn}} {{fadeOut}} {{reverseAudio}}
</template>

<script>
import DropboxSvg from './DropboxSvg.vue';
import api from '../axios'
import vClickOutside from 'v-click-outside'

export default {
    name: 'ConversionForm',
    components: {
        DropboxSvg
    },
    props: {
        page: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            files: [],
            conversionType: '',
            formats: '',
            selectedFormat: '',
            selectedFormatName: '',
            isConverting: false,
            conversionStatus: '',
            resize: false,
            stripMetadata: false,
            showFormats: false,
            showList: false,
            quality: 100,
            flip: null,
            rotate: null,
            audio: 100,
            frame_rate: null,
            width: null,
            height: null,
            fadeIn: false,
            fadeOut: false,
            reverseAudio: false,
            
            // pickerApiLoaded: false,
            // developerKey: "AIzaSyDDMide2ReC7teirBYymeZxGbMsaC9Ip7U",
            // clientId: "332837587267-uecej53nd36sd3v5lbrsb21m8m9np3so.apps.googleusercontent.com",
            // scope: "https://www.googleapis.com/auth/drive.readonly",
            // oauthToken: null
        }
    },
    mounted() {
        let dropBox = document.createElement("script");
        dropBox.setAttribute(
            "src",
            "https://www.dropbox.com/static/api/2/dropins.js"
        );
        dropBox.setAttribute("id", "dropboxjs");
        dropBox.setAttribute("data-app-key", "02vuzr3fulb6gy0");
        document.head.appendChild(dropBox);
        // let gDrive = document.createElement("script");
        // gDrive.setAttribute("type", "text/javascript");
        // gDrive.setAttribute("src", "https://apis.google.com/js/api.js");
        // document.head.appendChild(gDrive);
    },
    created() {
        if (!localStorage.getItem(`${this.page}Formats`)) {
            this.getFormats();
        } else {
            this.formats = JSON.parse(localStorage.getItem(`${this.page}Formats`));
            this.selectedFormat = this.formats[0].id;
        }
        this.setLocalStorage();
        if (this.formats && this.formats.length > 0) {
            this.selectedFormatName = this.formats[0].name;
            this.selectedFormat = this.formats[0].id;
        }
    },
    methods: {
        setLocalStorage() {
            if (!localStorage.getItem('pastConversions')) {
                localStorage.setItem('pastConversions', JSON.stringify({
                    'archive': [],
                    'audio': [],
                    'ebook': [],
                    'image': [],
                    'spreadsheet': [],
                    'video': [],
                }));
            }
        },
        async addFiles(event) {
            this.files = Array.from(event.target.files);
        },
        async addFileUrl(event) {
            event.stopPropagation();
            let url = prompt('Enter the url of the files (sepereated by comma)');
            if (url.includes(',')) {
                let urls = url.split(',');
                for (let i = 0; i < urls.length; i++) {
                    //trim the url
                    urls[i] = urls[i].trim();
                    let attachment = {};
                    attachment.name = urls[i];
                    attachment.title = urls[i];
                    this.files.push(attachment);
                }
            } else {
                let attachment = {};
                attachment.name = url;
                attachment.title = url;
                this.files.push(attachment);
            }
        },
        async dropboxIconClicked() {
            let options = {
                success: async files => {
                    let attachments = [];
                    for (let i = 0; i < files.length; i++) {
                        let attachment = {};
                        attachment.name = files[i].link;
                        attachment.title = files[i].name;
                        attachments.push(attachment);
                    }
                    this.files = attachments;

                    console.log(this.files);
                },

                cancel: function () { },

                linkType: "preview",
                multiselect: true,
                folderselect: false,
                sizeLimit: 102400000
            };
            Dropbox.choose(options);
        },
        triggerFileSelection() {
            this.$refs.fileInput.click();
        },
        removeFile(index) {
            this.files.splice(index, 1);
        },
        hideFormats() {
            this.showFormats = false;
        },
        resetConversion() {
            this.isConverting = false;
            this.files = [];
            this.conversionStatus = '';
        },
        getFormats() {
            api.get(`/formats/${this.page}`).then(response => {
                console.log(response.data);
                this.formats = response.data;
                this.storeFormats(response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        storeFormats(formatData) {
            if (!localStorage.getItem(`${this.page}Formats`)) {
                localStorage.setItem(`${this.page}Formats`, JSON.stringify(formatData));
            }
        },
        convertUrlFile() {
            let formData = new FormData();
            let selectedFormat = this.selectedFormat;
            let firstFileName = this.files[0].title;
            for (let i = 0; i < this.files.length; i++) {
                let file = JSON.stringify(this.files[i]);
                formData.append(`${this.page}[]`, file);
            }
            if (this.width && this.height) {
                formData.append('width', this.width);
                formData.append('height', this.height);
            }
            if (this.stripMetadata) {
                formData.append('strip_metadata', 1);
            }
            if(this.frame_rate) {
                formData.append('frame_rate', this.frame_rate);
            }
            if(this.rotate){
                formData.append('rotation_angle', this.rotate);
            }
            if(this.flip){
                formData.append('flip', this.flip);
            }
            if(this.page === 'video' || this.page === 'audio'){
                formData.append('audio_volume', this.audio);
            }
            if(this.page === 'image'){
                formData.append('quality', this.quality);
            }
            formData.append('format', selectedFormat);

            console.log(formData);
            api.post(`/conversions/url/${this.page}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.isConverting = true;
                this.conversionStatus = 'started';
                let guid = response.data.guid;
                this.subscribeToChannel(guid, firstFileName);
                console.log(response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        convertFile() {
            //check if an item in this.files is a url
            for (let i = 0; i < this.files.length; i++) {
                if (this.files[i].name.startsWith('http')) {
                    this.convertUrlFile();
                    return;
                }
            }
            let formData = new FormData();
            let selectedFormat = this.selectedFormat;
            let firstFileName = this.files[0].name;
            for (let i = 0; i <= this.files.length; i++) {
                formData.append(`${this.page}[]`, this.files[i]);
            }
            if (this.width && this.height) {
                formData.append('width', this.width);
                formData.append('height', this.height);
            }
            if (this.stripMetadata) {
                formData.append('strip_metadata', 1);
            }
            if(this.frame_rate) {
                formData.append('frame_rate', this.frame_rate);
            }
            if(this.rotate){
                formData.append('rotation_angle', this.rotate);
            }
            if(this.flip){
                formData.append('flip', this.flip);
            }
            if(this.page === 'video' || this.page === 'audio'){
                formData.append('audio_volume', this.audio);
                formData.append('fade_in', this.fadeIn);
                formData.append('fade_out', this.fadeOut);
            }
            if (this.page === 'audio'){
                formData.append('reverse_audio', this.reverseAudio);
            }
            if(this.page === 'image'){
                formData.append('quality', this.quality);
            }
            formData.append('format', selectedFormat);

            api.post(`/conversions/${this.page}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.isConverting = true;
                this.conversionStatus = 'started';
                let guid = response.data.guid;
                this.subscribeToChannel(guid, firstFileName);
                console.log(response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        subscribeToChannel(guid, firstFileName) {
            window.Echo.channel('conversion.' + guid)
                .listen('ImageConverted', (event) => {
                    this.conversionStatus = event.status;
                    if (event.status == 'completed') {
                        window.Echo.leave('conversion.' + guid);
                        this.addPastConversion(guid, this.page, firstFileName);
                        this.fetchConversion(guid);
                    }
                });
        },
        addPastConversion(sessionId, type, firstFileName) {
            let pastConversions = JSON.parse(localStorage.getItem('pastConversions'));
            const dateTime = new Date().toLocaleString();
            if (pastConversions[type]) {
                pastConversions[type].push({ guid: sessionId, filename: firstFileName, time: dateTime });
                localStorage.setItem('pastConversions', JSON.stringify(pastConversions));
            }
        },
        fetchConversion(sessionId) {
            api.get(`/${this.page}/` + sessionId + '.zip', { responseType: 'blob' })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'file.zip');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.log(error);
                });
        },
        dropFiles(event) {
            event.preventDefault();
            this.files = Array.from(event.dataTransfer.files);
        },
    },
    computed: {
        spinnerClass() {
            return {
                'text-secondary': this.conversionStatus === 'started',
                'text-primary': this.conversionStatus === 'processing',
                'text-success': this.conversionStatus === 'completed',
                'text-danger': this.conversionStatus === 'failed'
            }
        }
    }
}
</script>

<style scoped>
#dropzone {
    width: 200px;
    height: 200px;
}

.custum-file-upload {
    height: 267px;
    width: 300px;
    display: flex;
    flex-direction: column;
    align-items: space-between;
    gap: 20px;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    border: 2px dashed #e8e8e8;
    background-color: #212121;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0px 48px 35px -48px #e8e8e8;
}

.custum-file-upload .icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custum-file-upload .icon svg {
    height: 80px;
    fill: #e8e8e8;
}

.custum-file-upload .text {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custum-file-upload .text span {
    font-weight: 400;
    color: #e8e8e8;
}

.custum-file-upload input {
    display: none;
}

.clear {
    clear: both;
}

.checkBox {
    display: block;
    cursor: pointer;
    width: 30px;
    height: 30px;
    border: 3px solid rgba(255, 255, 255, 0);
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    box-shadow: 0px 0px 0px 2px #fff;
}

.checkBox div {
    width: 60px;
    height: 60px;
    background-color: #fff;
    top: -52px;
    left: -52px;
    position: absolute;
    transform: rotateZ(45deg);
    z-index: 100;
}

.checkBox input[type=checkbox]:checked+div {
    left: -10px;
    top: -10px;
}

.checkBox input[type=checkbox] {
    position: absolute;
    left: 50px;
    visibility: hidden;
}

.transition {
    transition: 300ms ease;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.arrow-down {
    margin-left: 5px;
}

.dropdown-list {
    /* display: none; */
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    padding: 12px 16px;
    z-index: 1;
}

.dropdown-list li {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.button {
    font-size: 17px;
    border-radius: 12px;
    background: linear-gradient(180deg, rgb(56, 56, 56) 0%, rgb(36, 36, 36) 66%, rgb(41, 41, 41) 100%);
    color: rgb(218, 218, 218);
    border: none;
    padding: 2px;
    font-weight: 700;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.button span {
    border-radius: 10px;
    padding: 0.8em 1.3em;
    padding-right: 1.2em;
    text-shadow: 0px 0px 20px #4b4b4b;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    color: inherit;
    transition: all 0.3s;
    background-color: rgb(29, 29, 29);
    background-image: radial-gradient(at 95% 89%, rgb(15, 15, 15) 0px, transparent 50%), radial-gradient(at 0% 100%, rgb(17, 17, 17) 0px, transparent 50%), radial-gradient(at 0% 0%, rgb(29, 29, 29) 0px, transparent 50%);
}

.button:hover span {
    background-color: rgb(26, 25, 25);
}

.button-overlay {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: repeating-conic-gradient(rgb(48, 47, 47) 0.0000001%, rgb(51, 51, 51) 0.000104%) 60% 60%/600% 600%;
    filter: opacity(10%) contrast(105%);
    -webkit-filter: opacity(10%) contrast(105%);
}

.button svg {
    width: 15px;
    height: 15px;
}

/* .input {
    width: 200px;
    height: 45px;
    border: none;
    outline: none;
    padding: 0px 7px;
    border-radius: 6px;
    color: #fff;
    font-size: 15px;
    background-color: transparent;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 1),
        -1px -1px 6px rgba(255, 255, 255, 0.4);
}

.input:focus {
    border: 2px solid transparent;
    color: #fff;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 1),
        -1px -1px 6px rgba(255, 255, 255, 0.4),
        inset 3px 3px 10px rgba(0, 0, 0, 1),
        inset -1px -1px 6px rgba(255, 255, 255, 0.4);
} */

.input {
    width: 100%; /* Ensure the input takes full width of its container */
    height: 45px;
    border: none;
    outline: none;
    padding: 0px 7px;
    border-radius: 6px;
    color: #fff;
    font-size: 15px;
    background-color: transparent;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 1),
        -1px -1px 6px rgba(255, 255, 255, 0.4);
}

.input:focus {
    border: 2px solid transparent;
    color: #fff;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 1),
        -1px -1px 6px rgba(255, 255, 255, 0.4),
        inset 3px 3px 10px rgba(0, 0, 0, 1),
        inset -1px -1px 6px rgba(255, 255, 255, 0.4);
}


/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}

.card {
    width: 25%;
    height: 35%;
    border-radius: 30px;
    background: #212121;
    box-shadow: 15px 15px 30px rgb(25, 25, 25),
        -15px -15px 30px rgb(60, 60, 60);
    overflow-y: auto;
    overflow-x: hidden;
}

/* Style scrollbar for Chrome, Safari and Opera */
.card::-webkit-scrollbar {
    width: 1em;
}

.card::-webkit-scrollbar-track {
    box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
}

.card::-webkit-scrollbar-thumb {
    background-color: darkgrey;
    outline: 1px solid slategrey;
}

/* Style scrollbar for IE and Edge */
.card {
    scrollbar-width: thin;
    scrollbar-color: darkgrey slategrey;
}

.position-absolute {
    position: absolute;
    z-index: 1;
}

svg {
    cursor: pointer;
}

@media only screen and (max-width: 767px) {
    .custum-file-upload {
        width: 100%;
        height: auto;
        padding: 1rem;
    }

    .checkBox {
        width: 20px;
        height: 20px;
    }

    .button {
        font-size: 14px;
    }

    .button span {
        padding: 0.5em 1em;
    }

    .input {
        width: 100%;
    }

    .card {
        width: 100%;
        height: auto;
    }
}

@media (max-width: 575px) {
    .cardholder {
        width: 75%;
        height: 25%;
        top: 25%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>