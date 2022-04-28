{{-- <div class="footer">
  <p>Copyright © 2022 Edu Meeting Co., Ltd. All Rights Reserved. 
      <br>
      Design: <a href="https://templatemo.com" target="_parent" title="free css templates">TemplateMo</a>
      <br>
      Distibuted By: <a href="https://themewagon.com" target="_blank" title="Build Better UI, Faster">ThemeWagon</a>
    </p>
</div> --}}

    <script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/clients/isotope.min.js')}}"></script>
    <script src="{{asset('assets/js/clients/owl-carousel.js')}}"></script>
    <script src="{{asset('assets/js/clients/lightbox.js')}}"></script>
    <script src="{{asset('assets/js/clients/tabs.js')}}"></script>
    <script src="{{asset('assets/js/clients/video.js')}}"></script>
    <script src="{{asset('assets/js/clients/slick-slider.js')}}"></script>
    <script src="{{asset('assets/js/clients/custom.js')}}"></script>

    <script>
        //according to loftblog tut
        $('.nav li:first').addClass('active');

        var showSection = function showSection(section, isAnimate) {
          var
          direction = section.replace(/#/, ''),
          reqSection = $('.section').filter('[data-section="' + direction + '"]'),
          reqSectionPos = reqSection.offset().top - 0;

          if (isAnimate) {
            $('body, html').animate({
              scrollTop: reqSectionPos },
            800);
          } else {
            $('body, html').scrollTop(reqSectionPos);
          }

        };

        var checkSection = function checkSection() {
          $('.section').each(function () {
            var
            $this = $(this),
            topEdge = $this.offset().top - 80,
            bottomEdge = topEdge + $this.height(),
            wScroll = $(window).scrollTop();
            if (topEdge < wScroll && bottomEdge > wScroll) {
              var
              currentId = $this.data('section'),
              reqLink = $('a').filter('[href*=\\#' + currentId + ']');
              reqLink.closest('li').addClass('active').
              siblings().removeClass('active');
            }
          });
        };

        $('.main-menu, .responsive-menu, .scroll-to-section').on('click', 'a', function (e) {
          e.preventDefault();
          showSection($(this).attr('href'), true);
        });

        $(window).scroll(function () {
          checkSection();
        });
    </script>