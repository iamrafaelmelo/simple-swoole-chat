const webSocket = new WebSocket('ws://127.0.0.1:8000');
const form = document.querySelector('form');
const input = document.querySelector('input');
const messages = document.querySelector('messages');

webSocket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    output(data);
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
                <span class="font-medium">${data.id ? `Other` : 'Me'}</span>: ${data.text}
            </p>
        </div>
    `);
};
