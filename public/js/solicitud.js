$(function () {
    const urlBase = "index.php";

    //Función para cargar las solicitudes pendientes
    function cargarSolicitudes() {
        $.ajax({
            url: urlBase,
            type: 'GET',
            data: { option: 'solicitudes_json' },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                let tbody = $("#solicitudes-body");
                tbody.empty();
                
                if (data.length === 0) {
                    tbody.append("<tr><td colspan='6' style='text-align:center;'>No hay solicitudes pendientes.</td></tr>");
                } else {
                    data.forEach(solicitud => {
                        let tr = $("<tr></tr>");
                        tr.append(`<td>${solicitud.id}</td>`);
                        tr.append(`<td>${solicitud.taller}</td>`);
                        tr.append(`<td>${solicitud.usuario}</td>`);
                        tr.append(`<td>${solicitud.fecha_solicitud}</td>`);
                        tr.append(`
                            <td>
                                <button class="btn-aprobar" data-id="${solicitud.id}" style="background-color: green; color: white; border: none; padding: 5px 10px; cursor: pointer;">Aprobar</button>
                                <button class="btn-rechazar" data-id="${solicitud.id}" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Rechazar</button>
                            </td>
                        `);
                        tbody.append(tr);
                    });
                }
            },
            error: function () {
                $("#mensaje").html("<p style='color:red;'>Error al cargar las solicitudes.</p>");
            }
        });
    }

    //Cargar al iniciar
    cargarSolicitudes();

    //Evento para Aprobar
    $("#tabla-solicitudes").on("click", ".btn-aprobar", function () {
        let solicitudId = $(this).data("id");
        procesarSolicitud('aprobar', solicitudId);
    });

    //Evento para Rechazar
    $("#tabla-solicitudes").on("click", ".btn-rechazar", function () {
        let solicitudId = $(this).data("id");
        if(confirm("¿Estás seguro de rechazar esta solicitud?")) {
            procesarSolicitud('rechazar', solicitudId);
        }
    });

    //Función centralizada para enviar la petición de aprobar o rechazar
    function procesarSolicitud(accion, id) {
        $.ajax({
            url: urlBase,
            type: 'POST',
            data: {
                option: accion,
                id_solicitud: id
            },
            dataType: 'json',
            success: function (response) {
                let mensajeDiv = $("#mensaje");
                if (response.success) {
                    mensajeDiv.html(`<p style='color:green;'>${response.message}</p>`);
                    cargarSolicitudes(); //Recargar la tabla sin refrescar la página
                } else {
                    mensajeDiv.html(`<p style='color:red;'>Error: ${response.error}</p>`);
                }
                
                //Ocultar mensaje después de 3 segundos 
                setTimeout(() => mensajeDiv.empty(), 3000);
            },
            error: function () {
                $("#mensaje").html("<p style='color:red;'>Error de comunicación con el servidor.</p>");
            }
        });
    }
});