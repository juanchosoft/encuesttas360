<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/include/header.php';
?>

<div class="menu-container">
  <div class="menu-header">
    <h2 style="color: #5b5a58;" >MENÚ</h2>
    <div class="search-bar">
      <input type="text" placeholder="¿Qué modulo necesita?">
    </div>
  </div>
  <div class="menu-grid">
    <!--INICIO OPCION DASHBOARD -->
    <a href="dashboard.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
          <line x1="3" y1="9" x2="21" y2="9" />
          <line x1="9" y1="21" x2="9" y2="9" />
          <line x1="15" y1="15" x2="15" y2="17" />
          <line x1="12" y1="13" x2="12" y2="17" />
          <line x1="18" y1="11" x2="18" y2="17" />
        </svg>
      </div>
      <div class="label">Dashboard</div>
    </a>
    <!-- INICIO OPCIÓN REGISTRO VISITA GOBERNADOR -->
    <!-- <a href="visitaGobernador.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
          <polyline points="14 2 14 8 20 8" />
          <line x1="16" y1="13" x2="8" y2="13" />
          <line x1="16" y1="17" x2="8" y2="17" />
          <polyline points="10 9 9 9 8 9" />
        </svg>
      </div>
      <div class="label">Registro visita gobernador</div>
    </a> -->
    <!-- INICIO OPCIÓN PRIMERA DAMA -->
    <!-- <a href="DamaMobile.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="10" width="2" height="10" />
          <rect x="7" y="6" width="2" height="14" />
          <rect x="11" y="2" width="2" height="18" />
          <polyline points="17 16 19 18 23 14" />
        </svg>
      </div>
      <div class="label">Primera dama</div>
    </a> -->
    <!-- INICIO OPCIÓN PLAN DE DESARROLLO -->
    <!-- <a href="plan_desarrollo.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16v16H4z" />
          <path d="M8 16l3-3l2 2l3-3" />
          <path d="M8 10h8" />
        </svg>
      </div>
      <div class="label">Plan de desarrollo</div>
    </a> -->
    <!-- INICIO OPCIÓN SECRETARÍAS -->
    <!-- <a href="secretariaMobile.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="5" y="4" width="14" height="16" rx="1"/>
          <path d="M9 8h6M9 12h6M9 16h4" stroke="#3B82F6"/>
          <path d="M14 5v2" stroke="#10B981"/>
        </svg>
      </div>
      <div class="label">Secretarías</div>
    </a> -->
    <!-- INICIO OPCIÓN ALCALDÍAS -->
    <!-- <a href="alcaldiasMobile.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M3 10h18" />
          <path d="M5 10v10" />
          <path d="M19 10v10" />
          <path d="M9 10v10" />
          <path d="M15 10v10" />
          <path d="M2 20h20" />
          <path d="M12 2l10 6H2l10-6z" />
        </svg>
      </div>
      <div class="label">Alcaldías</div>
    </a> -->
    <!-- INICIO OPCIÓN ACCIÓN UNIFICADA -->
    <!-- <a href="accionMobile.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2" stroke="#3B82F6"/>
          <path d="M15 5l3-3-3-3M15 19l3 3-3 3" stroke="currentColor"/>
          <circle cx="12" cy="12" r="4" stroke="#3B82F6" stroke-width="1.5"/>
          <path d="M12 8v8m4-4H8" stroke="currentColor"/>
        </svg>
      </div>
      <div class="label">Acción unificada</div>
    </a> -->
    <!-- INICIO OPCIÓN PROYECTOS ESTRATÉGICOS -->
    <!-- <a href="estrategicoMobile.php" class="menu-item-mobile">
      <div class="icon-mobile">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="16" rx="2" />
          <path d="M7 10l2 2l4-4" />
          <path d="M7 14l2 2l4-4" />
        </svg>
      </div>
      <div class="label">Proyectos estratégicos</div>
    </a> -->
  </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box; //tamaño de los elementos 
}

.menu-container {
    width: 100%;
    max-width: 400px;
    overflow: hidden;
    padding: 20px;
    margin: 150px auto;
}

.menu-header {
    margin-bottom: 15px;
}

.menu-header h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
}

.search-bar {
    position: relative;
    margin-bottom: 15px;
}

.search-bar input {
    width: 100%;
    padding: 12px 15px 12px 40px;
    border: none;
    background-color: #f7f7f7;
    border-radius: 25px;
    font-size: 14px;
    color:rgb(148, 119, 60);
}

.search-bar input::placeholder {
    color: #5b5a58;
}

.search-bar::before {
    content: "";
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: contain;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.menu-item-mobile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    transition: transform 0.2s;
    text-decoration: none;
}

.menu-item-mobile:hover {
    transform: translateY(-2px);
}

//Diseño de los iconos
.menu-item-mobile .icon-mobile {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    color:rgb(79, 122, 220); 
}

.menu-item-mobile .label {
    font-size: 13px;
    color: #333;
    font-weight: 500;
    flex: 1;
}

.banner {
    margin-top: 20px;
    width: 100%;
    height: 120px;
    background: linear-gradient(to right, #3b4cd9, #1a2abb);
    border-radius: 10px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 15px;
    color: white;
    position: relative;
    overflow: hidden;
}

.banner-content {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    max-width: 60%;
}

.banner-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
    line-height: 1.2;
}

.banner-subtitle {
    font-size: 12px;
    opacity: 0.9;
}

.banner-image {
    position: absolute;
    right: 15px;
    height: 90%;
    width: auto;
}
</style>

<script>
//FUNCIONALIDAD BARRA DE BUSQUEDA
//FUNCIONALIDAD BARRA DE BUSQUEDA
document.addEventListener("DOMContentLoaded", function() {
    // Obtiene referencias a los elementos
    const searchInput = document.querySelector('.search-bar input');
    const menuItems = document.querySelectorAll('.menu-item-mobile');
    
    // Añadir evento de entrada al campo de búsqueda
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Si no hay término de búsqueda, mostrar todos los elementos
        if (searchTerm === '') {
            menuItems.forEach(item => {
                item.style.display = 'flex';
            });
            return;
        }
        
        // Filtra los elementos del menú
        menuItems.forEach(item => {
            const label = item.querySelector('.label').textContent.toLowerCase();
            // Verificar si el texto del elemento contiene el término de búsqueda
            if (label.includes(searchTerm)) {
                item.style.display = 'flex'; // Muestra el elemento
            } else {
                item.style.display = 'none'; // Oculta el elemento
            }
        });
        
        // Comprobar si hay resultado visible
        const visibleItems = Array.from(menuItems).filter(item => item.style.display !== 'none');
        
        //Mostrar mensaje si no hay resultado
        const noResultsMessage = document.getElementById('no-results-message');
        if (visibleItems.length === 0) {
            //Mensaje de no hay resultados
            if (!noResultsMessage) {
                const message = document.createElement('p');
                message.id = 'no-results-message';
                message.textContent = 'No se encontraron módulos con ese nombre';
                message.style.textAlign = 'center';
                message.style.padding = '10px';
                message.style.color = '#666';
                
                // Insertamos el mensaje después de la barra de búsqueda
                searchInput.parentNode.parentNode.insertAdjacentElement('afterend', message);
            } else {
                noResultsMessage.style.display = 'block';
            }
        } else if (noResultsMessage) {
            // Ocultamos el mensaje si hay resultados
            noResultsMessage.style.display = 'none';
        }
    });
    
    // Añadir evento para limpiar la búsqueda al hacer clic en la X (para navegadores que muestran la X)
    searchInput.addEventListener('search', function() {
        if (this.value === '') {
            // Mostrar todos los elementos
            menuItems.forEach(item => {
                item.style.display = 'flex';
            });
            // Ocultar mensaje de no resultados si existe
            const noResultsMessage = document.getElementById('no-results-message');
            if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }
    });
});
</script>

<?php 
include 'admin/include/generic_script.php'; 
include 'admin/include/scriptsgober360.php'; 
?>
<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>