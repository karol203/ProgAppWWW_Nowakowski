let motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
if (motionQuery.matches) {
    $.fx.off = true;
}


let isExpandedA = false;

$('#anim-a').on('click', function() {
    if (isExpandedA === false) {
        isExpandedA = true;
        $(this).attr('aria-pressed', 'true');
        $(this).stop(true, false).animate({
            width: '240px',
            height: '130px'
        }, 300);
    } else {
        isExpandedA = false;
        $(this).attr('aria-pressed', 'false');
        $(this).stop(true, false).animate({
            width: '160px',
            height: '90px'
        }, 300);
    }
});
