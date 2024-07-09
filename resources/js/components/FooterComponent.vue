<template>
    <footer class="site-footer">

<div class="footer-container">
  <div class="container">
    <div class="row pt-3">
      <div class="col-sm-12 col-md-6">
        <h6>About</h6>
        <p class="text-justify">Transforming multimedia and data effortlessly, our website serves as a versatile hub
          for converting audio, video, image, and spreadsheet file formats with seamless precision.</p>
        <p class="text-justify">Whether it's refining audio clips, enhancing video quality, optimizing images, or 
          streamlining data in spreadsheets, our platform offers a user-friendly experience, ensuring efficiency and 
          accuracy in every conversion.</p>
        <p class="text-justify">Embracing cutting-edge technology, we empower users to effortlessly adapt their digital 
          content to meet diverse needs, facilitating smooth transitions across formats with just a few clicks.</p>
      </div>
      <div class="col-xs-6 col-md-3">
        <h6>Converters</h6>
        <ul class="footer-links">
          <li><router-link to="/archives" @click="scrollToTop">Archive</router-link></li>
          <li><router-link to="/audios" @click="scrollToTop">Audio</router-link></li>
          <li><router-link to="ebooks" @click="scrollToTop">E-book</router-link></li>
          <li><router-link to="/images" @click="scrollToTop">Images</router-link></li>
          <li><router-link to="/videos" @click="scrollToTop">Video</router-link></li>
          <li><router-link to="/spreadsheets" @click="scrollToTop">Spreadsheets</router-link></li>
        </ul>
      </div>
      <div class="col-xs-6 col-md-3">
        <h6>Quick Links</h6>
        <ul class="footer-links">
          <li><router-link to="/contact" @click="scrollToTop">Contact Us</router-link></li>
          <li><a href="https://buy.stripe.com/cN22bo61Dfhk5i04gg">Contribute</a></li>
          <li><router-link to="/privacy" @click="scrollToTop">Privacy Policy</router-link></li>
          <li><a href="/sitemap.xml">Sitemap</a></li>
        </ul>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8 col-sm-6 col-xs-12">
        <p class="copyright-text">Copyright &copy; 2024 All Rights Reserved by
          <a href="#">File Converter Services</a>.
        </p>
      </div>
    </div>
    <hr>
  </div>
</div>
</footer>
</template>

<script>
export default {
    name: 'FooterComponent',
    methods: {
        scrollToTop() {
            window.scrollTo(0, 0);
        }
    }
}
</script>