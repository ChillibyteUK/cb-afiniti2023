var data = {};
var scores = { 'Culture': 0, 'Leadership': 0, 'Method': 0, 'Engagement': 0, 'Drivers': 0, 'Capability': 0 };

// show first step

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('form0').style.display = 'block';
})

document.getElementById('step0').addEventListener('click',function(){
    document.getElementById('form0').style.display = 'none';
    document.getElementById('form1').style.display = 'block';
});
document.querySelectorAll('.start').forEach(function(button) {
    button.addEventListener('click', function(){
        document.getElementById('form0').style.display = 'none';
        document.getElementById('form1').style.display = 'block';
    });
});
document.querySelectorAll('.reset').forEach(function(button) {
    button.addEventListener('click', function(){
        document.getElementById('form0').style.display = 'block';
        document.getElementById('form1').style.display = 'none';
    });
});

//---------------------------------------------------------------------- step 2
// validate step 1

document.getElementById('step1').addEventListener('click',function(){
    const requiredFields = ['contactName','orgName','contactEmail','consent'];

    const isValid = [];
    requiredFields.every((x) => {
        if (validate(x) == true) {
            isValid.push(true)
        }
        else {
            isValid.push(false)
        }
        return true;
    })

    if (isValid.every(Boolean)) {

        data.contactName = document.getElementById('contactName').value;
        data.contactTitle = document.getElementById('contactTitle').value;
        data.orgName = document.getElementById('orgName').value;
        data.contactPhone = document.getElementById('contactPhone').value;
        data.contactMobile = document.getElementById('contactMobile').value;
        data.contactEmail = document.getElementById('contactEmail').value;
        data.consent = document.getElementById('consent').value;

        document.getElementById('form1').style.display = 'none';
        document.getElementById('form2').style.display = 'block';

        console.log('pushing event to dataLayer');
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'cra_contact_submit'
        });

        // console.log(data);
    }

})

//---------------------------------------------------------------------- step 2
// check select
document.getElementById('changeInProgress').addEventListener('change',function(){
    if (document.getElementById('changeInProgress').value == 'Yes') {
        document.getElementById('changeDetailContainer').style.display = 'grid';
    }
    else {
        document.getElementById('changeDetailContainer').style.display = 'none';
    }
});


document.getElementById('step2').addEventListener('click',function(){

    const requiredFields = ['changeInProgress','changeRole'];

    const isValid = [];

    requiredFields.every((x) => {
        if (validate(x) == true) {
            isValid.push(true)
        }
        else {
            isValid.push(false)
        }
        return true;
    })

    // if (document.getElementById('changeInProgress').value == 'Yes') {
    //     if (validate('changeDetail') == true) {
    //         isValid.push(true);
    //     }
    //     else {
    //         isValid.push(false);
    //     }
    // }

    // console.log('isValid ' + isValid);

    if (isValid.every(Boolean)) {

        data.changeInProgress = document.getElementById('changeInProgress').value;
        // data.changeDetail = document.getElementById('changeDetail').value;
        data.changeRole = document.getElementById('changeRole').value;

        document.getElementById('form2').style.display = 'none';
        document.getElementById('form3').style.display = 'block';

        // console.log(data);
    }

});

//---------------------------------------------------------------------- step 3

document.getElementById('step3').addEventListener('click',function(){

    if (checkAllRadioGroups('form3') === true) {
        // console.log('all answered');
        document.getElementById('form3Warn').style.display = 'none';
    }
    else {
        // console.log('missing answer');
        document.getElementById('form3Warn').style.display = 'block';
        return false;
    }

    UpdateScores(3);

    document.getElementById('form3').style.display = 'none';
    document.getElementById('form4').style.display = 'block';

    // console.log(data);
    // console.log(scores);

})

//---------------------------------------------------------------------- step 4

document.getElementById('step4').addEventListener('click',function(){

    if (checkAllRadioGroups('form4') === true) {
        // console.log('all answered');
        document.getElementById('form4Warn').style.display = 'none';
    }
    else {
        // console.log('missing answer');
        document.getElementById('form4Warn').style.display = 'block';
        return false;
    }

    UpdateScores(4);

    document.getElementById('form4').style.display = 'none';
    document.getElementById('form5').style.display = 'block';

    // console.log(scores);
    // console.log(data);

});

//---------------------------------------------------------------------- step 5

document.getElementById('step5').addEventListener('click',function(){

    if (checkAllRadioGroups('form5') === true) {
        // console.log('all answered');
        document.getElementById('form5Warn').style.display = 'none';
    }
    else {
        // console.log('missing answer');
        document.getElementById('form5Warn').style.display = 'block';
        return false;
    }

    UpdateScores(5);

    // document.getElementById('form5').style.display = 'none';

    // console.log(scores);
    // console.log(data);

    document.getElementById('data').value = JSON.stringify(data);
    document.getElementById('scores').value = JSON.stringify(scores);

    return true;
})

//------------------------------------------------------------------- functions

// functions

function validate(field) {
    // console.log('field '+field+' value is: ' + document.getElementById(field).value);
    if (document.getElementById(field).type === 'text'
     || document.getElementById(field).type === 'email') {
        if (document.getElementById(field).value == "") {
            document.getElementById(field + 'Warn').style.display = 'block';
            return false;
        }
        else {
            document.getElementById(field + 'Warn').style.display = 'none';
            return true;
        }
    }
    else if (document.getElementById(field).type === 'checkbox') {
        if (document.getElementById(field).checked != true) {
            document.getElementById(field + 'Warn').style.display = 'block';
            return false;
        }
        else {
            document.getElementById(field + 'Warn').style.display = 'none';
            return true;
        }
    }
    else if (document.getElementById(field).type === 'select-one') {
        if (document.getElementById(field).value == false) {
            document.getElementById(field + 'Warn').style.display = 'block';
            return false;
        }
        else {
            document.getElementById(field + 'Warn').style.display = 'none';
            return true;
        }
    }
    else if (document.getElementById(field).type === 'textarea') {
        if(document.getElementById(field).value == '') {
            // console.log(field + ' value is null ' + document.getElementById(field).value); 
            document.getElementById(field + 'Warn').style.display = 'block';
            return false;
        }
        else {
            document.getElementById(field + 'Warn').style.display = 'none';
            return true;
        }
    }
    else {
        console.log('ERR: not handled ' + document.getElementById(field).type);
    }
}

function getVal(field) {
    let answer;
    let lever;
    for (var i = 0; i < field.length; i++) {
        if (field[i].checked) {
            answer = field[i].value;
            lever = field[i].getAttribute('data-lever');
            break;
        }
    }
    return [lever, answer];
}

function checkAllRadioGroups(container) {
    var divElement = document.getElementById(container);
    var radioGroups = divElement.querySelectorAll('input[type="radio"]');
    var radioGroupNames = new Set();

    // Collect unique radio group names
    radioGroups.forEach(function(radio) {
        radioGroupNames.add(radio.name);
    });

    // Check if all radio groups have a selected value
    for (var name of radioGroupNames) {
        var radios = document.getElementsByName(name);
        var hasSelectedValue = false;

        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                hasSelectedValue = true;
                break;
            }
        }

        if (!hasSelectedValue) {
            return false; // At least one radio group doesn't have a value
        }
    }

    return true; // All radio groups have a value
}

function UpdateScores(form) {
    // console.log(scores);
    for (i = 1; i <= 6; i++) {
        var [lever,ans] = getVal( document.getElementsByName('form'+form+'_answers_' + i) );
        let curr = scores[lever];
        // console.log('curr '+lever+': '+curr);
        scores[lever] = Number(curr) + Number(ans);
    }
    return;
}
