document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.btn-archive').forEach((element) => {
    element.addEventListener('click', (event) => {
      if (confirm("Êtes vous sûr de vouloir archiver ce random Santa ?")) {
        callApi(event.target.dataset['path'], 'DELETE', '/santa');
      }
    });
  });

  document.querySelectorAll('.btn-add-to-santa, .btn-calculate-santa').forEach((element) => {
    element.addEventListener('click', (event) => {
      callApi(event.target.dataset['path'], 'POST', document.location.href);
    });
  });

  document.querySelectorAll('.btn-remove-to-santa').forEach((element) => {
    element.addEventListener('click', (event) => {
      callApi(event.target.dataset['path'], 'DELETE', document.location.href);
    });
  });
});

function callApi(path, method, href) {
  fetch(path, {
    method
  }).then(() => {
    document.location.href = href
  }).catch((error) => {
    console.log(error);
  });
}
