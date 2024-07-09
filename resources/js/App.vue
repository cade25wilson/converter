<template>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="navbar-toggler" @click.prevent="closeNavbar" type="button" data-bs-toggle="collapse"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link to="/archives" class="nav-link text-default">Archive</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/audios" class="nav-link text-default">Audio</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/ebooks" class="nav-link text-default">E-book</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/images" class="nav-link text-default" aria-current="page">Images</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/spreadsheets" class="nav-link text-default" aria-disabled="true">Spreadsheet</router-link>
          </li>
          <li class="nav-item">
            <router-link to='/videos' class="nav-link text-default">Video</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/previousconversions" class="nav-link text-default" aria-disabled="true">Previous
              Conversions</router-link>
          </li>
          <li class="nav-item" v-if="os=='Windows'">
            <href src="/FileConverter.exe" class="nav-link text-default" aria-disabled="true">Download App</href>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
    <router-view></router-view>
  </div>
  <!-- Site footer -->
  <FooterComponent />
</template>

<script>
import { defineAsyncComponent } from 'vue';

export default {
  name: 'App',
  components: {
    FooterComponent: defineAsyncComponent(() => import('./components/FooterComponent.vue'))
  },
  data() {
    return {
      os: ''
    }
  },
  mounted() {
    this.os = this.getOS();
  },
  methods: {
    closeNavbar() {
      console.log('closenavbar');
      let navbar = document.getElementById('navbarNav');
      if (navbar.classList.contains('show')) {
        navbar.classList.remove('show');
      } else {
        navbar.classList.add('show');
      }
    },
    getOS() {
      if (navigator.userAgentData && navigator.userAgentData.platform) {
        return navigator.userAgentData.platform;
      }
      const userAgent = window.navigator.userAgent;
      if (/Win32|Win64|Windows|WinCE/.test(userAgent)) {
        return 'Windows';
      }
    }
  }
}
</script>