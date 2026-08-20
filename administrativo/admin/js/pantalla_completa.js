document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalSvgFull');

  if (!modal) return;

  modal.addEventListener('show.bs.modal', function () {
    const svgOriginal = document.querySelector('#contenido-mapa svg');
    const svgContainer = document.getElementById('svg-container-full');

    if (!svgOriginal || !svgContainer) return;

    // Limpiar el contenedor
    svgContainer.innerHTML = '';

    // Clonar el SVG original
    const clone = svgOriginal.cloneNode(true);

    // Configurar el viewBox por defecto (ajustable según necesidad)
    const defaultViewBox = '-120 500 2400 2';
    clone.setAttribute('viewBox', defaultViewBox);
    clone.removeAttribute('width');
    clone.removeAttribute('height');
    clone.setAttribute('preserveAspectRatio', 'xMidYMid meet');

    // Estilos para ocupar toda la pantalla
    clone.style.width = '100%';
    clone.style.height = 'auto';
    clone.style.minHeight = '100vh';
    clone.style.display = 'block';

    svgContainer.style.overflow = 'auto';

    // Insertar SVG clonado
    svgContainer.appendChild(clone);
  });
});
