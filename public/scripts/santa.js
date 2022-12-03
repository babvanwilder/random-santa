document.addEventListener("DOMContentLoaded", () => {
  document.querySelector('.btn-archive').addEventListener('click', (event) => {
    if (confirm("Êtes vous sûr de vouloir archiver ce random Santa ?")) {
      let path = event.target.dataset['path']
      fetch(path, {
        method: 'DELETE'
      }).then((response) => {
        document.location.href = '/santa'
      }).catch((error) => {
        console.log(error);
      });
    }
  });
});
