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

let isHoveredB = false;
let isFocusedB = false;

function updateAnimB() {
    if (isHoveredB === true || isFocusedB === true) {
        $('#anim-b').stop(true, false).animate({
            width: '220px',
            height: '120px'
        }, 300);
    } else {
        $('#anim-b').stop(true, false).animate({
            width: '160px',
            height: '90px'
        }, 300);
    }
}

$('#anim-b').on('mouseenter', function() {
    isHoveredB = true;
    updateAnimB();
});

$('#anim-b').on('mouseleave', function() {
    isHoveredB = false;
    updateAnimB();
});

$('#anim-b').on('focus', function() {
    isFocusedB = true;
    updateAnimB();
});

$('#anim-b').on('blur', function() {
    isFocusedB = false;
    updateAnimB();
});
