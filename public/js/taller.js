$(function () {
    const urlBase = "index.php";

    //Función para cargar los talleres desde el servidor y mostrarlos en la tabla
    function cargarTalleres() {
        $.ajax({
            url: urlBase,
            type: 'GET',
            data: { option: 'talleres_json' },
            dataType: 'json',
            success: function (data) {
                let tbody = $("<tbody></tbody>");
                
                //Si no hay talleres con cupo disponible, mostrar mensaje
                if (data.length === 0) {
                    tbody.append("<tr><td colspan='4' class='text-center'>No hay talleres con cupo disponible en este momento.</td></tr>");
                } else {
                    //Generar las filas de la tabla con los talleres disponibles
                    data.forEach(taller => {
                        let tr = $("<tr></tr>");
                        tr.append(`<td>${taller.nombre}</td>`);
                        tr.append(`<td>${taller.descripcion}</td>`);
                        tr.append(`<td>${taller.cupo_disponible} / ${taller.cupo_maximo}</td>`);
                        
                        let btnHtml = '';
                        if (taller.ya_solicitado) {
                            //Si ya lo pidió, vemos si está pendiente o si ya se lo aprobaron
                            if (taller.estado_solicitud === 'aprobada') {
                                btnHtml = `<button class="btn btn-secondary btn-sm" disabled style="opacity: 0.7; cursor: not-allowed;">¡Inscrito!</button>`;
                            } else {
                                btnHtml = `<button class="btn btn-warning btn-sm" disabled style="opacity: 0.8; cursor: not-allowed; color: black;">Pendiente</button>`;
                            }
                        } else {
                            //Si no lo ha pedido, mostramos el botón normal
                            btnHtml = `<button class="btn btn-success btn-sm btn-solicitar" data-id="${taller.id}">
                                           Solicitar Inscripción
                                       </button>`;
                        }

                        tr.append(`<td>${btnHtml}</td>`);
                        tbody.append(tr);
                    });
                }

                //Limpia la tabla y agrega el nuevo contenido
                $(".table").empty().append(`
                    <thead>
                        <tr>
                            <th>Taller</th>
                            <th>Descripción</th>
                            <th>Cupo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                `).append(tbody);
            },
            error: function () {
                alert("Error al cargar los talleres.");
            }
        });
    }

    //Cargar talleres al iniciar la página
    cargarTalleres();

    //Evento delegado para el botón "Solicitar" (Delegado porque los botones se generan dinámicamente)
    $(".table").on("click", ".btn-solicitar", function () {
        let tallerId = $(this).data("id");
        let btn = $(this);

        //Deshabilitar botón para evitar doble clic
        btn.prop('disabled', true).text('Solicitando...');

        $.ajax({
            url: urlBase,
            type: 'POST',
            data: {
                option: 'solicitar',
                taller_id: tallerId
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    cargarTalleres(); //Recargar la tabla para ver cambios en cupos
                } else {
                    alert("Error: " + response.error);
                    btn.prop('disabled', false).text('Solicitar Inscripción');
                }
            },
            error: function () {
                alert("Error de comunicación con el servidor.");
                btn.prop('disabled', false).text('Solicitar Inscripción');
            }
        });
    });
});