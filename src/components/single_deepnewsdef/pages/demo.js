const viewer = new Viewer(document.getElementById('imgArchitecture'), {
  inline: false,
  toolbar: {
    zoomIn: 4,
    zoomOut: 4,
    oneToOne: 4,
    reset: 4,
  },  
  viewed() {
    viewer.zoomTo(1);
  },
});

const viewer1 = new Viewer(document.getElementById('alg1mask'), {
  inline: false,
  toolbar: {
    zoomIn: 4,
    zoomOut: 4,
    oneToOne: 4,
    reset: 4,
  },  
  viewed() {
    viewer.zoomTo(1);
  },
});
