document.addEventListener('DOMContentLoaded', function(){
  // Example: add ripple effect to quick-action buttons
  document.querySelectorAll('.quick-actions .action').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      btn.classList.add('active');
      setTimeout(()=>btn.classList.remove('active'), 200);
    });
  });
});