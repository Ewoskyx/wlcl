function animateValue(el, start = 0, end = 0, is_plus="false", duration = 143500) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;

        const progress = Math.min((timestamp - startTimestamp) / 2000, 1);
        el.innerHTML = prettyPrice(Math.floor(progress * (end - start) + start), is_plus)

        // if not at end, continue
        // if at end, return final number WITHOUT math operation to preserve decimals
        if (progress < 1) window.requestAnimationFrame(step);
        else el.innerHTML = this.prettyPrice(end, is_plus)
    };
    window.requestAnimationFrame(step);
}

function prettyPrice(value = 0, is_plus) {
    if (value == 0 && is_plus) return '+0';
    if (value == 0 && !is_plus) return '0';

    // preserve string and exit, no need for currency conversion
    if (!Number(value)) return value;

    if (value / 1000 >= 1) {
        value = value / 1000;
        if(is_plus != "false"){
            return '+' + Math.round(value * 10) / 10 + 'K';
        }else{
            return Math.round(value * 10) / 10 + 'K';
        }
    }else{
        if(is_plus != "false"){
            return '+' + Math.round(value * 10) / 10;
        }else{
            return Math.round(value * 10) / 10;
        }
    }
}
