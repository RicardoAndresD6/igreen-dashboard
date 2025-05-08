$(document).ready(function () {
    function actualizarDatos() {
        $.ajax({
            url: 'index.php?ajax=true',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (!data.error) {
                    $('#suelo1-value').text(data.suelo1 + '%');
                    $('#suelo2-value').text(data.suelo2 + '%');
                    $('#humedad-value').text(data.humedad + '%');
                    $("#nivel_agua-value").text(data.nivel_agua + '%');
                    $('#temperatura-value').text(data.temperatura + '°C');
                    $('#hora-actualizacion').text('Actualizado: ' + data.fecha);

                    // Validación de humedad del suelo
                    var suelo1 = parseInt(data.suelo1);
                    var suelo2 = parseInt(data.suelo2);

                    var humedad_suelo1 = suelo1 < 30 ? 'text-red-500' : 'text-green-500';
                    var humedad_suelo2 = suelo2 < 30 ? 'text-red-500' : 'text-green-500';

                    $('#suelo1-value').removeClass('text-green-500 text-red-500').addClass(humedad_suelo1);
                    $('#suelo2-value').removeClass('text-green-500 text-red-500').addClass(humedad_suelo2);

                    // Mostrar/ocultar mensaje de nivel bajo
                    if (suelo1 < 30) {
                        $('#suelo1-value').siblings('.nivel-bajo-msg').show();
                    } else {
                        $('#suelo1-value').siblings('.nivel-bajo-msg').hide();
                    }

                    if (suelo2 < 30) {
                        $('#suelo2-value').siblings('.nivel-bajo-msg').show();
                    } else {
                        $('#suelo2-value').siblings('.nivel-bajo-msg').hide();
                    }

                    // Actualizar clases de temperatura y nivel de agua
                    $('#temperatura-value').removeClass('bg-green-500 bg-red-500').addClass(data.temp_estado);
                    $('#nivel_agua-value').removeClass('bg-green-500 bg-red-500').addClass(data.nivel_agua_estado);

                } else {
                    console.error('Error en los datos:', data.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos del servidor:', error);
            }
        });
    }

    actualizarDatos();
    setInterval(actualizarDatos, 1000);
    
    $('#btn-riego').on('click', function () {
        console.log('Escribiendo comando en comando.txt...');
    
        $.ajax({
            url: 'index.php?ajax=escribir_comando',
            method: 'POST',
            success: function (res) {
                try {
                    const response = JSON.parse(res);
                    console.log('Respuesta del servidor:', response);
    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            confirmButtonColor: '#22c55e'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                } catch (error) {
                    console.error('Error al procesar la respuesta del servidor:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error inesperado',
                        text: 'No se pudo procesar la respuesta del servidor.'
                    });
                }
            },
            error: function () {
                console.error('Error al realizar la solicitud al servidor');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor.'
                });
            }
        });
    });

});