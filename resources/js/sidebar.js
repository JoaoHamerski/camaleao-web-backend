import { setCookie } from '@/helpers'

$('#btnSidebar').click(function() {
  $(this).toggleClass('is-active')
  $('.sidebar').toggleClass('is-active')

  if ($(window).width() > 576) {
    if ($('.sidebar').hasClass('is-active')) {
      setCookie({name: 'sidebar_active', value: true })
    } else {
      setCookie({name: 'sidebar_active', value: false })
    }
  } else {
    if (! $('.sidebar').hasClass('is-active')) {
      setCookie({name: 'sidebar_active', value: false})
    }
  }
})

$(document).on('touchstart', function(e) {
  const touchStartX = e.touches[0].clientX

  if (touchStartX < 40 && ! $('.sidebar').hasClass('is-active') ||
    ($('.sidebar').hasClass('is-active') && touchStartX  > $(document).width() - 200)) {
    $(document).on('touchmove', function(e) {
      let touchMoveX = (e.touches[0].clientX * 100) / $(document).width()
      touchMoveX = -100 +touchMoveX

      if (touchMoveX < -10) {
        $('.sidebar').css({'margin-left' : touchMoveX + '%'})
      }
    })
  }
})

$(document).on('touchend', function() {
  $('.sidebar').removeAttr('style')

  if (typeof window.touchMoveX !== 'undefined' && window.touchMoveX != null) {
    if (! $('.sidebar').hasClass('is-active')) {
      $('.sidebar').css({'margin-left' : '0%', transition: 'all .25s'})
      $('.sidebar').addClass('is-active')
      $('#btnSidebar').addClass('is-active')
    } else if ($('.sidebar').hasClass('is-active')) {
      $('.sidebar').css({'margin-left' : '-100%', transition: 'all .25s'})
      $('.sidebar').removeClass('is-active')
      $('#btnSidebar').removeClass('is-active')
    }
  }

  $(document).unbind('touchmove')
  window.touchMoveX = null
})

$('.sidebar').on('transitionend MSTransitionEnd webkitTransitionEnd oTransitionEnd', function() {
  if ($(window).width() < 576) {
    $(this).css({transition : 'none'})
  }
})
