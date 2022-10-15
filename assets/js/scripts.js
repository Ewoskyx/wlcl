(function(window, undefined) {
  'use strict';

  /*
  NOTE:
  ------
  PLACE HERE YOUR OWN JAVASCRIPT CODE IF NEEDED
  WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR JAVASCRIPT CODE PLEASE CONSIDER WRITING YOUR SCRIPT HERE.  */


})(window);

$(function () {
  var includes = $('[data-include]')
  $.each(includes, function () {
    var file = 'partials/' + $(this).data('include') + '.html'
    $(this).load(file)
  })

  new PerfectScrollbar(".widget-chat-scroll", {
    wheelPropagation: !1
  });

  function widgetMessageSend(source) {
    var message = $(".widget-chat-message").val();
    if (message != "") {
      var html = '<div class="chat"><div class="chat-body"><div class="chat-message">' + "<p>" + message + "</p>" + "<div class=" + "chat-time" + ">3:35 AM</div></div></div></div>";
      $(".widget-chat-messages .chat-content").append(html);
      $(".widget-chat-message").val("");
      $(".widget-chat-scroll").scrollTop($(".widget-chat-scroll > .chat-content").height());
    }
  }

  $('.add-packagesTwo2').repeater({
    show: function () {
      var selfRepeaterItem = this;

      $(this).slideDown();
      var repeaterItems = $(".add-packagesTwo > .row");
      var random_string = (Math.random() + 1).toString(36).substring(7);
      $(selfRepeaterItem).find('.sevk_area label').attr('for','label_' + random_string);
      $(selfRepeaterItem).find('.sevk_area input').attr('id','label_' + random_string);

    },
    hide: function (deleteElement) {
      $(this).slideUp(deleteElement);
    }
  });

  $(document).on('change','.is_adr_sevk',function () {
    if(this.checked) {
      $(this).parents('.row').find('.is_adr_sevk').show();
    }else{
      $(this).parents('.row').find('.is_adr_sevk').hide();
    }
  });

  $('.add-packagesTwo').repeater({
    show: function () {
      var selfRepeaterItem = this;

      $(this).slideDown();
      var repeaterItems = $(".add-packagesTwo2 > .row");
      var random_string2 = (Math.random() + 1).toString(36).substring(7);
      $(selfRepeaterItem).find('.sevk_area2 label').attr('for','label_' + random_string2);
      $(selfRepeaterItem).find('.sevk_area2 input').attr('id','label_' + random_string2);

    },
    hide: function (deleteElement) {
      $(this).slideUp(deleteElement);
    }
  });

  $(document).on('change','.is_adr',function () {
    if(this.checked) {
      $(this).parents('.item').find('.is_adr').show();
    }else{
      $(this).parents('.item').find('.is_adr').hide();
    }
  });

  $(document).on('click','.secimKutusu',function (){
    var value = $(this).find('h5').html();
    $(this).parents('.item').find('#pack').html(value)
  });

})