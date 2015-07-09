'use strict';

app.filter('travisSetStatusIcon', setStatusIcon);
app.filter('travisSetDescription', setDescription);

function setStatusIcon() {
    return function(state) {
        if(state === 'passed' || state === 'fixed') {
            return 'fa-check';
        } else if(state === 'broken' || state === 'failed') {
            return 'fa-remove';
        } else {
            return 'fa-gears';
        }
    };
}

function setDescription() {
    return function(message) {
        var number = '',
            url = '',
            type = '';

        if(extras.git_url) {
            url = '<a href="' + message.extras.git_url + '">{{number}}</a>';
        }

        if(message.extras.type) {
            type = message.extras.type.replace('_', ' ');
        }

        if(message.extras.type === 'push') {
            number = message.extras.git_number ? message.extras.git_number.substr(0, 10) : '';
        } else {
            number = message.extras.git_number ? '#' + message.extras.git_number : '';
        }

        return type + ' ' + url.replace('{{number}}', number);
    }
}
