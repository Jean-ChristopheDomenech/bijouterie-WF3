// Rubberband Input
const rubberIpts = document.querySelectorAll(".rubber-ipt");

for (var i = 0; i < rubberIpts.length; i++) {
    const rubberRange = rubberIpts[i].querySelector(".rubber-ipt-range");
    const rubberMin = rubberIpts[i].querySelector(".rubber-ipt-min");
    const rubberMax = rubberIpts[i].querySelector(".rubber-ipt-max");
    var initialMousePosMin;
    var initialMousePosMax;


    // Rubber Minimum
    rubberMin.style.left = '0px'
    function dragTargetMin(dragOffsetMin) {
        rubberMin.style.left = `${dragOffsetMin}px`;
    }
    function getDragOffsetMin(e) {
        if (initialMousePosMin == null) {
            initialMousePosMin = e.clientX;
        }
        var mousePos = e.clientX;
        var dragOffsetMin = mousePos - initialMousePosMin;
        var rubberMinMax = 200 + (parseInt(rubberMax.style.left, 10)) - 10;

        if (dragOffsetMin < 0){dragOffsetMin = 0}
        else if (dragOffsetMin > rubberMinMax) {
            dragOffsetMin = rubberMinMax;
        };
        if (dragOffsetMin > 190) {
            dragOffsetMin = 190;
        }

        dragTargetMin(dragOffsetMin);
        updateRubberRangeMin(dragOffsetMin);
        getMinPrice(dragOffsetMin);
    }

    function SetDragStartMin(e) {
        document.addEventListener("mousemove", getDragOffsetMin);
    }
    function stopDragMin() {
        document.removeEventListener("mousemove", getDragOffsetMin);
    }

    rubberMin.addEventListener("mousedown", SetDragStartMin);
    document.addEventListener("mouseup", stopDragMin);


    // Rubber Maximum
    rubberMax.style.left = '0px'
    function dragTargetMax(dragOffsetMax) {
        rubberMax.style.left = `${dragOffsetMax}px`;
    }
    function getDragOffsetMax(e) {
        if (initialMousePosMax == null) {
            initialMousePosMax = e.clientX;
        }
        var mousePos = e.clientX;
        var dragOffsetMax = mousePos - initialMousePosMax;
        var rubberMaxMin = (parseInt(rubberMin.style.left, 10) - 200 + 10);

        if (dragOffsetMax > 0){dragOffsetMax = 0}
        else if (dragOffsetMax < rubberMaxMin) {
            dragOffsetMax = rubberMaxMin;
        };
        if (dragOffsetMax < -190) {
            dragOffsetMax = -190;
        };

        dragTargetMax(dragOffsetMax);
        updateRubberRangeMax(dragOffsetMax);
        getMaxPrice(dragOffsetMax);
    }

    function SetDragStartMax() {
        document.addEventListener("mousemove", getDragOffsetMax);
    }
    function stopDragMax() {
        document.removeEventListener("mousemove", getDragOffsetMax);
    }

    rubberMax.addEventListener("mousedown", SetDragStartMax);
    document.addEventListener("mouseup", stopDragMax);


    // Update Range between Min and Max

    rubberRange.style.width = '200px';
    function updateRubberRangeMin(dragOffsetMin){
        rubberRange.style.left = `${dragOffsetMin}px`;

        var rubberRangeWidth = 200 - ((parseInt(rubberMax.style.left, 10)) * -1) - dragOffsetMin;
        if (rubberRangeWidth <= 0) {
            rubberRangeWidth = 0;
        }
        rubberRange.style.width = `${rubberRangeWidth}px`;
    }
    function updateRubberRangeMax(dragOffsetMax){
        var rubberRangeWidth = 200 - parseInt(rubberMin.style.left, 10) - (dragOffsetMax * -1);
        if (rubberRangeWidth <= 0) {
            rubberRangeWidth = 0;
        }
        rubberRange.style.width = `${rubberRangeWidth}px`;
    }

    // Update price range

    const minPrice = rubberIpts[i].querySelector(".rubber-value-min");
    const maxPrice = rubberIpts[i].querySelector(".rubber-value-max");
    const prixMini= document.getElementById('minprice');
    const prixMaxi= document.getElementById('maxprice');


    var RubberMinPrice = 100;
    var RubberMaxPrice = 500;

    function getMinPrice(dragOffsetMin) {
        rubberMinPrice = ((RubberMaxPrice/200) * dragOffsetMin) + (((RubberMinPrice - ((RubberMinPrice/200) * dragOffsetMin)))) + "€";
        prixMini.value= `${rubberMinPrice}`;
        minPrice.innerHTML = `${rubberMinPrice}`;

    }
    function getMaxPrice(dragOffsetMax) {
        rubberMaxPrice = ((RubberMaxPrice/200) * (dragOffsetMax + 200)) + ((RubberMinPrice - ((RubberMinPrice/200) * (dragOffsetMax + 200))))+ "€";
        prixMaxi.value=`${rubberMaxPrice}`
        maxPrice.innerHTML = `${rubberMaxPrice}`

    }
};
