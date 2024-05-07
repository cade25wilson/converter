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
      <!-- Site footer -->
      <footer class="site-footer">
      
      <div class="footer-container">
        <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <h6>About</h6>
            <p class="text-justify">Transforming multimedia and data effortlessly, our website serves as a versatile hub for converting audio, video, image, and spreadsheet file formats with seamless precision. Whether it's refining audio clips, enhancing video quality, optimizing images, or streamlining data in spreadsheets, our platform offers a user-friendly experience, ensuring efficiency and accuracy in every conversion. Embracing cutting-edge technology, we empower users to effortlessly adapt their digital content to meet diverse needs, facilitating smooth transitions across formats with just a few clicks.</p>
          </div>
          <div class="col-xs-6 col-md-3">
            <h6>Converters</h6>
            <ul class="footer-links">
              <li><router-link to="/images">Images</router-link></li>
              <li><router-link to="/audios">Audio</router-link></li>
              <li><router-link to="/videos">Video</router-link></li>
              <li><router-link to="/spreadsheets">Spreadsheets</router-link></li>
            </ul>
          </div>
          <div class="col-xs-6 col-md-3">
            <h6>Quick Links</h6>
            <ul class="footer-links">
              <li><router-link to="/about">About</router-link></li>
              <li><router-link to="/contact">Contact Us</router-link></li>
              <li><a href="https://buy.stripe.com/cN22bo61Dfhk5i04gg">Contribute</a></li>
              <li><router-link to="/privacy">Privacy Policy</router-link></li>
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