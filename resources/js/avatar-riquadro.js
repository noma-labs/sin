// The square box drawn on a photo to cut the avatar of a persona (nomadelfia/persone/avatar-riquadro): drag on the
// photo to draw it, drag it to move it, drag its corner to resize it, or use the arrows (with Shift to resize). The box
// goes to the hidden x, y, w, h of the form normalized to the photo, and the preview shows the avatar it cuts.

const LATO_MINIMO = 16;
const LATO_ANTEPRIMA = 128;
const PASSO_TASTIERA = 4;

function avviaRiquadro(contenitore) {
  const foto = contenitore.querySelector("img");
  const box = contenitore.querySelector("[data-riquadro-box]");
  const maniglia = box.querySelector("[data-riquadro-maniglia]");
  const form = document.getElementById(contenitore.dataset.avatarRiquadro);
  const anteprima = form.querySelector("[data-riquadro-anteprima]");

  // the box in pixels of the photo as shown
  let riquadro = null;
  let trascinamento = null;

  const misure = () => ({
    larghezza: foto.clientWidth,
    altezza: foto.clientHeight,
  });

  function limita() {
    const { larghezza, altezza } = misure();
    const lato = Math.min(
      Math.max(riquadro.lato, LATO_MINIMO),
      larghezza,
      altezza,
    );
    riquadro = {
      x: Math.min(Math.max(riquadro.x, 0), larghezza - lato),
      y: Math.min(Math.max(riquadro.y, 0), altezza - lato),
      lato,
    };
  }

  function disegna() {
    limita();
    const { larghezza, altezza } = misure();
    const { x, y, lato } = riquadro;

    Object.assign(box.style, {
      left: `${x}px`,
      top: `${y}px`,
      width: `${lato}px`,
      height: `${lato}px`,
    });
    box.hidden = false;

    form.elements.x.value = (x / larghezza).toFixed(6);
    form.elements.y.value = (y / altezza).toFixed(6);
    form.elements.w.value = (lato / larghezza).toFixed(6);
    form.elements.h.value = (lato / altezza).toFixed(6);

    const scala = LATO_ANTEPRIMA / lato;
    Object.assign(anteprima.style, {
      backgroundImage: `url("${foto.currentSrc || foto.src}")`,
      backgroundSize: `${larghezza * scala}px ${altezza * scala}px`,
      backgroundPosition: `${-x * scala}px ${-y * scala}px`,
    });
  }

  // from the normalized box of the form (or the initial one), so it survives a resize of the window
  function daNormalizzato(normalizzato) {
    const { larghezza, altezza } = misure();
    riquadro = {
      x: normalizzato.x * larghezza,
      y: normalizzato.y * altezza,
      lato: normalizzato.w * larghezza,
    };
  }

  function inizia() {
    const iniziale = contenitore.dataset.iniziale
      ? JSON.parse(contenitore.dataset.iniziale)
      : null;
    if (iniziale) {
      daNormalizzato(iniziale);
    } else {
      const { larghezza, altezza } = misure();
      const lato = Math.min(larghezza, altezza) / 2;
      riquadro = { x: (larghezza - lato) / 2, y: (altezza - lato) / 3, lato };
    }
    disegna();
  }

  function punto(evento) {
    const area = foto.getBoundingClientRect();
    return { x: evento.clientX - area.left, y: evento.clientY - area.top };
  }

  contenitore.addEventListener("pointerdown", (evento) => {
    if (evento.button !== 0 || !riquadro) {
      return;
    }
    evento.preventDefault();
    const p = punto(evento);
    const dentro =
      p.x >= riquadro.x &&
      p.x <= riquadro.x + riquadro.lato &&
      p.y >= riquadro.y &&
      p.y <= riquadro.y + riquadro.lato;

    if (evento.target === maniglia) {
      trascinamento = { modo: "ingrandisci" };
    } else if (dentro) {
      trascinamento = {
        modo: "sposta",
        dx: p.x - riquadro.x,
        dy: p.y - riquadro.y,
      };
    } else {
      trascinamento = { modo: "disegna", ax: p.x, ay: p.y };
    }
    contenitore.setPointerCapture(evento.pointerId);
    box.focus({ preventScroll: true });
  });

  contenitore.addEventListener("pointermove", (evento) => {
    if (!trascinamento) {
      return;
    }
    const p = punto(evento);
    if (trascinamento.modo === "sposta") {
      riquadro = {
        ...riquadro,
        x: p.x - trascinamento.dx,
        y: p.y - trascinamento.dy,
      };
    } else if (trascinamento.modo === "ingrandisci") {
      riquadro = {
        ...riquadro,
        lato: Math.max(p.x - riquadro.x, p.y - riquadro.y),
      };
    } else {
      const { ax, ay } = trascinamento;
      const lato = Math.max(Math.abs(p.x - ax), Math.abs(p.y - ay));
      riquadro = {
        x: p.x < ax ? ax - lato : ax,
        y: p.y < ay ? ay - lato : ay,
        lato,
      };
    }
    disegna();
  });

  const fine = (evento) => {
    trascinamento = null;
    if (contenitore.hasPointerCapture(evento.pointerId)) {
      contenitore.releasePointerCapture(evento.pointerId);
    }
  };
  contenitore.addEventListener("pointerup", fine);
  contenitore.addEventListener("pointercancel", fine);

  box.addEventListener("keydown", (evento) => {
    const direzioni = {
      ArrowLeft: [-1, 0],
      ArrowRight: [1, 0],
      ArrowUp: [0, -1],
      ArrowDown: [0, 1],
    };
    const direzione = direzioni[evento.key];
    if (!direzione) {
      return;
    }
    evento.preventDefault();
    const [dx, dy] = direzione;
    if (evento.shiftKey) {
      riquadro = {
        ...riquadro,
        lato: riquadro.lato + (dx + dy) * PASSO_TASTIERA,
      };
    } else {
      riquadro = {
        ...riquadro,
        x: riquadro.x + dx * PASSO_TASTIERA,
        y: riquadro.y + dy * PASSO_TASTIERA,
      };
    }
    disegna();
  });

  new ResizeObserver(() => {
    if (riquadro && foto.clientWidth > 0) {
      daNormalizzato({
        x: Number(form.elements.x.value),
        y: Number(form.elements.y.value),
        w: Number(form.elements.w.value),
      });
      disegna();
    }
  }).observe(foto);

  if (foto.complete && foto.naturalWidth > 0) {
    inizia();
  } else {
    foto.addEventListener("load", inizia, { once: true });
  }
}

document.querySelectorAll("[data-avatar-riquadro]").forEach(avviaRiquadro);
