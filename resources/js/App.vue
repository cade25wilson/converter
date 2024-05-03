<template>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <router-link to="/images" class="nav-link text-default" aria-current="page">Images</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/audios" class="nav-link text-default">Audio</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to='/videos' class="nav-link text-default" href="#">Video</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/spreadsheets" class="nav-link text-default" aria-disabled="true">Spreadsheet</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/previousconversions" class="nav-link text-default" aria-disabled="true">Previous Conversions</router-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <router-view></router-view>
    </div>
</template>