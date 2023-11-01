<script>
$(function() {
  $('#setting').mouseover(function(e) {
    $('#setting-menu', this).stop().slideDown('fast');
  })
  .mouseout(function(e) {
    $('#setting-menu', this).stop().slideUp('fast');
  });
});
</script>
