const webSocket = new WebSocket('ws://127.0.0.1:8000');
const form = document.querySelector('form');
const input = document.querySelector('input');
const messages = document.querySelector('messages');
const typing = document.querySelector('typing');
let typingTimeout;

input.addEventListener('keyup', function (event) {
    if (event.code !== 'Enter' || event.code !== 'F5') {
        typingTimeout = setTimeout(function () {
            webSocket.send('typing...');
        }, 300);
    }
});

input.addEventListener('keydown', function () {
    clearTimeout(typingTimeout);
    typing.textContent = '';
});

webSocket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    const typingText = `User ${data.id} is typing...`;

    if (data.text === typingText) {
        typing.textContent = typingText;
    } else {
        output(data);
    }
};

form.onsubmit = (event) => {
    event.preventDefault();
    const message = input.value;
    const json = JSON.stringify({
        text: message,
    });

    webSocket.send(message);
    output(JSON.parse(json));
    input.value = '';
};

const output = (data) => {
    messages.insertAdjacentHTML('beforeend', `
        <div class="mb-4 w-full text-${data.id ? 'left' : 'right'}">
            <p class="px-2.5 py-1 break-all inline-block border ${data.id ? 'bg-white border-slate-200' : 'bg-green-100 border-green-300'} rounded text-slate-600">
                <span class="font-medium">${data.id ? `User ${data.id}` : 'Me'}</span>: ${data.text}
            </p>
        </div>
    `);

    messages.scrollTo(0, messages.scrollHeight);
};
