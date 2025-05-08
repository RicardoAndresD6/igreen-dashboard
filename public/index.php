<?php
include_once './../src/config/config.php';
include_once './../src/config/firebaseRDB.php';

function obtenerDatos() {
    $firebase = new firebaseRDB(FIREBASE_RDB['url']);
    $data = $firebase->retrieve("igreen");

    if ($data) {
        $data = json_decode($data, true);
        $primerRegistro = reset($data); 
        echo json_encode($primerRegistro);
    } else {
        echo json_encode(["error" => "No se encontraron datos."]);
    }
    exit;
}

function escribirComando() {
  $archivo = __DIR__ . '/../comando.txt'; // Ruta al archivo comando.txt en la raíz
  $contenido = "ACTIVAR_RIEGO" . PHP_EOL; // Comando a escribir

  if (file_put_contents($archivo, $contenido, FILE_APPEND)) {
      echo json_encode(['status' => 'success', 'message' => 'Comando escrito en comando.txt']);


      sleep(5); // Esperar 5 segundos
      file_put_contents($archivo, '');
  } else {
      echo json_encode(['status' => 'error', 'message' => 'No se pudo escribir en comando.txt']);
  }


  exit;
}

function activarRiego() {
  $firebase = new firebaseRDB(FIREBASE_RDB['url']);

  $result = $firebase->update('igreen', 'data_riego_manual', [
      'riego_manual' => '1',
      'fecha' => date('Y-m-d H:i:s')
  ]);

  if ($result) {
      echo json_encode(['status' => 'success', 'message' => 'Riego activado']);
  } else {
      echo json_encode(['status' => 'error', 'message' => 'No se pudo activar el riego']);
  }
  exit;
}

if (isset($_GET['ajax'])) {
  if ($_GET['ajax'] === 'true') {
      obtenerDatos();
  } elseif ($_GET['ajax'] === 'activar_riego') {
      activarRiego();
  }else if( $_GET['ajax'] === 'escribir_comando') {
    escribirComando();
  }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Sistema de Riego Igreen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="images/igreen.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="assets/js/app.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body class="min-h-screen bg-black text-white">
    <!-- Header con logo -->
    <header class="bg-black glass-effect sticky top-0 z-10 shadow-lg border-b-2 card-border-color">
      <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-2 sm:space-x-3">
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full flex items-center justify-center overflow-hidden">
            <img src="images/igreen.png" alt="Logo AISA" class="w-full h-8 sm:h-10" />
          </div>
          <h1 class="text-lg sm:text-xl md:text-2xl font-bold tracking-tight">Sistema de Riego <span class="text-green-c">IGreen</span></h1>
        </div>
        
        <!-- Última actualización con icono -->
        <?php $hora_actual = isset($fecha) ? $fecha : date("d/m/Y H:i:s"); ?>
        <div class="hidden sm:flex items-center text-xs sm:text-sm text-green-c">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Actualizado: <?php echo $hora_actual; ?></span>
        </div>
      </div>
    </header>

    <!-- Dashboard principal -->
    <main class="container mx-auto px-2 sm:px-4 py-4 sm:py-6">
      <div class="grid grid-cols-1 gap-4 sm:gap-6">
        
        <!-- Panel de resumen -->
        <section class="bg-opacity-50 glass-effect rounded-xl shadow-lg p-3 sm:p-4 md:p-6 ui-card border-2 card-border-color card-background-color">
          <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-6 flex items-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-green-c" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Monitoreo en Tiempo Real
          </h2>
          
          <!-- Tarjetas de sensores -->
          <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4 lg:gap-5">
            <!-- Sensor Humedad Suelo 1 -->
            <div class="sensor-card ui-card border-2 card-border-color card-background-color rounded-lg p-3 sm:p-4 md:p-5 text-center shadow-lg">
              <div class="flex justify-between items-start mb-1 sm:mb-2">
                <h3 class="text-base sm:text-lg font-medium text-green-100">Suelo 1</h3>
                <span class="inline-flex h-2 w-2 sm:h-3 sm:w-3 rounded-full"></span>
              </div>
              <div class="mt-2 mb-1 sm:mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 md:h-10 md:w-10 mx-auto text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
              </div>
              <div class="text-2xl sm:text-3xl md:text-4xl font-bold" id="suelo1-value">%</div>
              <p class="text-xs text-green-200 mt-1 sm:mt-2">Humedad del Suelo</p>
                <div class="mt-1 sm:mt-3 text-xs text-red-300 nivel-bajo-msg" style="display: none;">¡Nivel bajo! Se recomienda riego</div>
            </div>
            
            <!-- Sensor Humedad Suelo 2 -->
            <div class="sensor-card ui-card border-2 card-border-color card-background-color rounded-lg p-3 sm:p-4 md:p-5 text-center shadow-lg">
              <div class="flex justify-between items-start mb-1 sm:mb-2">
                <h3 class="text-base sm:text-lg font-medium text-green-100">Suelo 2</h3>
                <span class="inline-flex h-2 w-2 sm:h-3 sm:w-3 rounded-full"></span>
              </div>
              <div class="mt-2 sm:mt-1 mb-1 sm:mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 md:h-10 md:w-10 mx-auto text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
              </div>
              <div class="text-2xl sm:text-3xl md:text-4xl font-bold" id="suelo2-value">%</div>
              <p class="text-xs text-green-200 mt-1 sm:mt-2">Humedad del Suelo</p>
              <div class="mt-1 sm:mt-3 text-xs text-red-300 nivel-bajo-msg" style="display: none;">¡Nivel bajo! Se recomienda riego</div>
            </div>
            
            <!-- Sensor Humedad Aire -->
            <div class="sensor-card ui-card border-2 card-border-color card-background-color rounded-lg p-3 sm:p-4 md:p-5 text-center shadow-lg">
              <div class="flex justify-between items-start mb-1 sm:mb-2">
                <h3 class="text-base sm:text-lg font-medium text-green-100">Nivel Agua</h3>
                <span class="inline-flex h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-green-500"></span>
              </div>
              <div class="mt-2 sm:mt-1 mb-1 sm:mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 md:h-10 md:w-10 mx-auto text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                </svg>
              </div>
              <div class="text-2xl sm:text-3xl md:text-4xl font-bold" id="nivel_agua-value">%</div>
              <p class="text-xs text-green-200 mt-1 sm:mt-2">Humedad del Aire</p>
            </div>
            
            <!-- Sensor Temperatura -->
            <div class="sensor-card ui-card border-2 card-border-color card-background-color rounded-lg p-3 sm:p-4 md:p-5 text-center shadow-lg">
              <div class="flex justify-between items-start mb-1 sm:mb-2">
                <h3 class="text-base sm:text-lg font-medium text-green-100">Temperatura</h3>
                <span class="inline-flex h-2 w-2 sm:h-3 sm:w-3 rounded-full <?php echo $temp_estado; ?>"></span>
              </div>
              <div class="mt-2 sm:mt-1 mb-1 sm:mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 md:h-10 md:w-10 mx-auto text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <div class="text-2xl sm:text-3xl md:text-4xl font-bold" id="temperatura-value">°C</div>
              <p class="text-xs text-green-200 mt-1 sm:mt-2">Temperatura Ambiente</p>
            </div>
          </div>
        </section>
        
        <!-- Panel de control -->
        <section class="bg-opacity-50 glass-effect rounded-xl p-3 sm:p-4 md:p-6 shadow-lg ui-card border-2 card-border-color card-background-color">
          <h2 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-4 flex items-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-green-c" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Panel de Control
          </h2>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6">
            <!-- Botón de riego manual -->
            <div class="p-3 sm:p-4 sensor-card ui-card border-2 card-border-color card-background-color rounded-lg text-center shadow-lg">
              <h3 class="text-base sm:text-lg font-medium mb-2 sm:mb-3 text-green-100">Riego Manual</h3>
              <p class="text-xs sm:text-sm text-gray-300 mb-3 sm:mb-4">Activa el sistema de riego de forma manual durante 5 minutos.</p>
              
                <button id="btn-riego" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-medium py-2 sm:py-3 px-3 sm:px-5 rounded-lg shadow-lg flex items-center justify-center space-x-1 sm:space-x-2 transition-all duration-300 transform hover:-translate-y-1 pulse-animation cursor-pointer">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  <span id="estado_riego" class="text-sm sm:text-base">Activar Riego Manual</span>
                </button>
            </div>
            
            <!-- Información del sistema -->
            <div class="p-3 sm:p-4 sensor-card ui-card border-2 card-border-color card-background-color rounded-lg text-center shadow-lg">
              <h3 class="text-base sm:text-lg font-medium mb-2 sm:mb-3 text-green-100">Estado del Sistema</h3>
              <ul class="space-y-1 sm:space-y-2 text-xs sm:text-sm">
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-green-500 mr-2"></span>
                  <span>Sistema activo y funcionando</span>
                </li>
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-gray-400 mr-2"></span>
                  <span>Próximo riego programado: 06:00 AM</span>
                </li>
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-gray-400 mr-2"></span>
                  <span>Último mantenimiento: <?php echo date('d/m/Y', strtotime('-7 days')); ?></span>
                </li>
              </ul>
              
              <button onclick="location.reload()" class="mt-3 sm:mt-4 w-full bg-gray-700 hover:bg-gray-600 text-white py-1 sm:py-2 px-3 sm:px-4 rounded-lg flex items-center justify-center text-xs sm:text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Actualizar Datos
              </button>
            </div>
          </div>
        </section>
      </div>
    </main>
    
    <!-- Footer -->
    <footer class="mt-2 pt-2 sm:pt-3 pb-2 bg-black bg-opacity-80 glass-effect text-center text-xs sm:text-sm text-green-100 border-t-2 card-border-color">
      <div class="container mx-auto px-4">
        <p>Sistema de Riego Automático IGreen &copy; <?php echo date('Y'); ?></p>
        <p class="text-xs mt-1 text-green-200">Última actualización: <?php echo isset($fecha) ? $fecha : "-"; ?></p>
      </div>
    </footer>
  </body>
</html>