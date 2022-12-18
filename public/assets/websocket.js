const webSocket = new WebSocket('ws://127.0.0.1:8000');
const form = document.querySelector('#form');
const inputMessage = document.querySelector('#message');
const messages = document.querySelector('#messages');

const addMessage = (data) => {
    messages.innerHTML += `
        <div class="mb-4 w-full text-${data.id ? 'left' : 'right'}">
            <p class="px-2.5 py-1 break-all inline-block border ${data.id ? 'bg-white border-slate-200' : 'bg-green-100 border-green-300'} rounded text-slate-600">
                <span class="font-medium">${data.id ? `Anonymous ${data.id}` : 'Me'}</span>: ${data.message}
            </p>
        </div>
    `;
};

form.addEventListener('submit', (event) => {
    event.preventDefault();
    const message = inputMessage.value;
    const json = JSON.stringify({
        message: message,
    });

    if (!message) {
        return;
    }

    inputMessage.value = '';
    webSocket.send(json);
    addMessage(JSON.parse(json));
});

webSocket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    addMessage(data);
};
