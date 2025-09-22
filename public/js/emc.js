(function(){
  // Persist activity pin state in localStorage
  const PIN_KEY = 'emc.activity.pin';
  function initPin(){
    const btn = document.querySelector('.activity .pin');
    if(!btn) return;
    const pinned = localStorage.getItem(PIN_KEY) === '1';
    if(pinned) btn.classList.add('active');
    btn.addEventListener('click', ()=>{
      const isActive = btn.classList.toggle('active');
      localStorage.setItem(PIN_KEY, isActive ? '1' : '0');
    });
  }

  // Close/open sidebar on small screens (optional)
  function initSidebarToggle(){
    // Could add a toggle later if needed
  }

  document.addEventListener('DOMContentLoaded', function(){
    initPin();
    initSidebarToggle();
  });
})();
