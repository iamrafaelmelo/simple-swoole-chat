const webSocket = new WebSocket('ws://127.0.0.1:8000');
const form = document.querySelector('form');
const input = document.querySelector('input');
const messages = document.querySelector('messages');
const typing = document.querySelector('typing');
let typingTimeout = null;

input.addEventListener('keyup', function (event) {
    const data = JSON.stringify({
        type: 'typing',
        message: null,
    });

    if (event.code !== 'Enter' || event.code !== 'F5') {
        typingTimeout = setTimeout(function () {
            webSocket.send(data);
        }, 300);
    }
});

input.addEventListener('keydown', function () {
    clearTimeout(typingTimeout);
    typing.textContent = '';
});

webSocket.onmessage = (event) => {
    const data = JSON.parse(event.data);

    if (data.type === 'typing') {
        return typing.textContent = data.message;
    }

    return output(data);
};

form.onsubmit = (event) => {
    event.preventDefault();
    const message = input.value;
    const data = JSON.stringify({
        type: 'default',
        message: message,
    });

    webSocket.send(data);
    output(JSON.parse(data));
    input.value = '';
};

const output = (data) => {
    messages.insertAdjacentHTML('beforeend', `
        <div class="mb-4 w-full text-${data.id ? 'left' : 'right'}">
            <p class="px-2.5 py-1 break-all inline-block border ${data.id ? 'bg-white border-slate-200' : 'bg-green-100 border-green-300'} rounded text-slate-600">
                <span class="font-medium">${data.id ? `Other` : 'Me'}</span>: ${data.message}
            </p>
        </div>
    `);

    messages.scrollTo(0, messages.scrollHeight);
};
