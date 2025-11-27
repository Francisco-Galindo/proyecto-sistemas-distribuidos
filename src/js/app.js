//AJAX para los chats
// Variables globales
let chatActivo = null;
let intervaloActualizacion = null;
let intervaloConversaciones = null;
let ultimoTotalMensajes = 0;

// Inicializar cuando carga la página
document.addEventListener("DOMContentLoaded", function () {
  cargarConversaciones();

  // Actualizar conversaciones cada 0.5 segundos
  intervaloConversaciones = setInterval(cargarConversaciones, 500);

  // Solicitar permiso para notificaciones
  if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission();
  }
});

// Cargar lista de conversaciones
function cargarConversaciones() {
  fetch("./procesos/obtener_conversaciones.php", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarConversaciones(data.conversaciones);
        verificarNuevosMensajes(data.conversaciones);
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Mostrar conversaciones en la lista
function mostrarConversaciones(conversaciones) {
  const contenedor = document.getElementById("listaConversaciones");

  if (conversaciones.length === 0) {
    contenedor.innerHTML =
      '<div class="sin-conversaciones">No tienes conversaciones aún.<br>Busca usuarios para iniciar un chat.</div>';
    return;
  }

  let html = "";
  conversaciones.forEach((conv) => {
    const mensajeCorto = conv.ultimo_mensaje
      ? conv.ultimo_mensaje.length > 50
        ? conv.ultimo_mensaje.substring(0, 50) + "..."
        : conv.ultimo_mensaje
      : "Sin mensajes";

    const badgeHtml =
      conv.mensajes_sin_leer > 0
        ? `<div class="badge-no-leidos">${conv.mensajes_sin_leer}</div>`
        : "";

    html += `
            <div class="conversacion-item" onclick="iniciarChat(${
              conv.id_usuario
            }, '${conv.usuario}')">
                <div class="conversacion-info">
                    <div class="conversacion-nombre">${conv.usuario}</div>
                    <div class="conversacion-ultimo">${escapeHtml(
                      mensajeCorto
                    )}</div>
                </div>
                ${badgeHtml}
            </div>
        `;
  });

  contenedor.innerHTML = html;
}

// Verificar si hay nuevos mensajes y mostrar notificación
function verificarNuevosMensajes(conversaciones) {
  const totalMensajesNuevos = conversaciones.reduce(
    (total, conv) => total + parseInt(conv.mensajes_sin_leer),
    0
  );

  // Si hay más mensajes que antes, mostrar notificación
  if (totalMensajesNuevos > ultimoTotalMensajes && ultimoTotalMensajes !== 0) {
    const conversacionConNuevos = conversaciones.find(
      (c) => c.mensajes_sin_leer > 0
    );

    if (conversacionConNuevos) {
      mostrarNotificacion(
        conversacionConNuevos.usuario,
        conversacionConNuevos.ultimo_mensaje || "Nuevo mensaje"
      );

      // Reproducir sonido (opcional)
      reproducirSonido();
    }
  }

  ultimoTotalMensajes = totalMensajesNuevos;

  // Actualizar título de la página
  if (totalMensajesNuevos > 0) {
    document.title = `(${totalMensajesNuevos}) Sistema de Chat`;
  } else {
    document.title = "Sistema de Chat";
  }
}

// Mostrar notificación del navegador
function mostrarNotificacion(titulo, mensaje) {
  if ("Notification" in window && Notification.permission === "granted") {
    new Notification(titulo, {
      body: mensaje,
      icon: "💬",
      badge: "💬",
    });
  }
}


// Buscar usuarios
function buscarUsuarios() {
  const termino = document.getElementById("terminoBusqueda").value;
  const formData = new FormData();
  formData.append("termino", termino);

  fetch("./procesos/buscar_usuario.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarResultados(data.resultados);
      } else {
        alert(data.error || "Error en la búsqueda");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al realizar la búsqueda");
    });
}

function mostrarResultados(resultados) {
  const contenedor = document.getElementById("resultadosBusqueda");
  const lista = document.getElementById("listaResultados");

  if (resultados.length > 0) {
    let html = "<ul>";
    resultados.forEach((usuario) => {
      html += `<li>
                ${usuario.usuario} 
                <small>(Se unió: ${usuario.fecha_creacion})</small>
                <button onclick="iniciarChat(${usuario.id_usuario}, '${usuario.usuario}')">💬 Chatear</button>
            </li>`;
    });
    html += "</ul>";
    lista.innerHTML = html;
    contenedor.style.display = "block";
  } else {
    lista.innerHTML =
      "<p>No se encontraron usuarios que coincidan con la búsqueda.</p>";
    contenedor.style.display = "block";
  }
}

// Iniciar chat con un usuario
function iniciarChat(idUsuario, nombreUsuario) {
  chatActivo = {
    id: idUsuario,
    nombre: nombreUsuario,
  };

  // Crear o mostrar ventana de chat
  let ventanaChat = document.getElementById("ventanaChat");

  if (!ventanaChat) {
    ventanaChat = document.createElement("div");
    ventanaChat.id = "ventanaChat";
    ventanaChat.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 400px;
            height: 500px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 9999;
        `;

    ventanaChat.innerHTML = `
            <div style="background: #0077b6; color: white; padding: 15px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <strong id="nombreContacto"></strong>
                <button onclick="cerrarChat()" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">✕</button>
            </div>
            <div id="mensajesContenedor" style="flex: 1; overflow-y: auto; padding: 15px; background: #f5f5f5;"></div>
            <div style="padding: 10px; background: #f0f0f0; display: flex; gap: 5px;">
                <input type="text" id="inputMensaje" placeholder="Escribe un mensaje..." 
                    style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"
                    onkeypress="if(event.key === 'Enter') enviarMensaje()">
                <button onclick="enviarMensaje()" 
                    style="padding: 10px 20px; background: #0077b6; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Enviar
                </button>
            </div>
        `;

    document.body.appendChild(ventanaChat);
  }

  document.getElementById("nombreContacto").textContent = nombreUsuario;
  ventanaChat.style.display = "flex";

  // Cargar mensajes
  cargarMensajes();

  // Actualizar mensajes cada 0.5 segundos
  if (intervaloActualizacion) {
    clearInterval(intervaloActualizacion);
  }
  intervaloActualizacion = setInterval(cargarMensajes, 500);

  // Actualizar lista de conversaciones para quitar el badge
  setTimeout(cargarConversaciones, 500);
}

// Cerrar ventana de chat
function cerrarChat() {
  const ventanaChat = document.getElementById("ventanaChat");
  if (ventanaChat) {
    ventanaChat.style.display = "none";
  }

  if (intervaloActualizacion) {
    clearInterval(intervaloActualizacion);
  }

  chatActivo = null;
}

// Cargar mensajes de la conversación
function cargarMensajes() {
  if (!chatActivo) return;

  const formData = new FormData();
  formData.append("id_contacto", chatActivo.id);

  fetch("./procesos/obtener_mensaje.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarMensajes(data.mensajes);
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Mostrar mensajes en pantalla
function mostrarMensajes(mensajes) {
  const contenedor = document.getElementById("mensajesContenedor");

  if (mensajes.length === 0) {
    contenedor.innerHTML =
      '<p style="text-align: center; color: #666;">No hay mensajes. ¡Inicia la conversación!</p>';
    return;
  }

  let html = "";
  mensajes.forEach((msg) => {
    const esMio = msg.de_usuario_id != chatActivo.id;
    const alineacion = esMio ? "flex-end" : "flex-start";
    const bgColor = esMio ? "#dcf8c6" : "#ffffff";

    const fecha = new Date(msg.fecha_creacion);
    const hora = fecha.toLocaleTimeString("es-MX", {
      hour: "2-digit",
      minute: "2-digit",
    });

    html += `
            <div style="display: flex; justify-content: ${alineacion}; margin-bottom: 10px;">
                <div style="max-width: 70%; padding: 10px; border-radius: 8px; background: ${bgColor}; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    <p style="margin: 0; word-wrap: break-word;">${escapeHtml(
                      msg.mensaje
                    )}</p>
                    <small style="color: #666; font-size: 11px;">${hora}</small>
                </div>
            </div>
        `;
  });

  contenedor.innerHTML = html;
  contenedor.scrollTop = contenedor.scrollHeight;
}

// Enviar mensaje
function enviarMensaje() {
  if (!chatActivo) return;

  const input = document.getElementById("inputMensaje");
  const mensaje = input.value.trim();

  if (mensaje === "") return;

  const formData = new FormData();
  formData.append("id_destinatario", chatActivo.id);
  formData.append("mensaje", mensaje);

  fetch("./procesos/enviar_mensaje.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        input.value = "";
        cargarMensajes();
        cargarConversaciones(); // Actualizar lista de conversaciones
      } else {
        alert("Error al enviar: " + (data.error || "Error desconocido"));
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al enviar el mensaje");
    });
}

// Función para escapar HTML (prevenir XSS)
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}


//ocultar o mostrar contraseña
function togglePassword(inputId, button) {
    //const pw = document.getElementById("contrasena");
    //const btn = document.querySelector(".toggle-pw");
    const pw = inputId ? document.getElementById(inputId) : document.getElementById("contrasena");
    const btn = button ? button : document.querySelector(".toggle-pw");

if (pw.type === "password") {
    pw.type = "text";
    btn.textContent = "Ocultar";
    } else {
        pw.type = "password";
        btn.textContent = "Mostrar";
    }
}