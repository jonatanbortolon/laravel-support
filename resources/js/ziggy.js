const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"requests.index":{"uri":"\/","methods":["GET","HEAD"]},"requests.create":{"uri":"chamado\/criar","methods":["GET","HEAD"]},"requests.post":{"uri":"chamado","methods":["POST"]},"requests.put":{"uri":"chamado\/{id}","methods":["PUT"]},"requests.detail":{"uri":"chamado\/{id}","methods":["GET","HEAD"]},"requests.comments.post":{"uri":"chamado\/{id}\/comentario","methods":["POST"]},"login.index":{"uri":"entrar","methods":["GET","HEAD"]},"login.post":{"uri":"entrar","methods":["POST"]},"register.index":{"uri":"cadastrar","methods":["GET","HEAD"]},"register.post":{"uri":"cadastrar","methods":["POST"]},"logout.index":{"uri":"sair","methods":["GET","HEAD"]}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
