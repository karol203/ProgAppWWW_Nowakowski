
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

let stepsC = 0;
let baseWidthC = 160;
let baseHeightC = 90;

$('#anim-c').on('click', function() {
    if ($(this).is(':animated') || stepsC >= 5) {
        return;
    }

    stepsC = stepsC + 1;
    $('#status-c').text('Kroki obiektu C: ' + stepsC + ' / 5');

    let newWidth = baseWidthC + (stepsC * 20);
    let newHeight = baseHeightC + (stepsC * 10);

    $(this).animate({
        width: newWidth + 'px',
        height: newHeight + 'px'
    }, 300);
});

$('#reset-ac').on('click', function() {
    isExpandedA = false;
    $('#anim-a').stop(true, false).css({
        width: '160px',
        height: '90px'
    }).attr('aria-pressed', 'false');

    stepsC = 0;
    $('#anim-c').stop(true, false).css({
        width: '160px',
        height: '90px'
    });
    $('#status-c').text('Kroki obiektu C: 0 / 5');
});

$('#check-server').on('click', function() {
    let przycisk = $(this);
    let poleStatusu = $('#server-status');

    przycisk.prop('disabled', true);
    poleStatusu.text('Ładowanie...');

    $.ajax({
        url: 'status.php',
        method: 'GET',
        dataType: 'text',
        timeout: 5000
    })
    .done(function(odpowiedz) {
        poleStatusu.text(odpowiedz);
    })
    .fail(function() {
        poleStatusu.text('Wystąpił błąd podczas łączenia z serwerem.');
    })
    .always(function() {
        przycisk.prop('disabled', false);
    });
});
