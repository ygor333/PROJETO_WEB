
function mostrarErro(campo, mensagem) {
  limparErro(campo);
  campo.classList.add("is-invalid");
  const div = document.createElement("div");
  div.className = "invalid-feedback";
  div.textContent = mensagem;
  campo.insertAdjacentElement("afterend", div);
}

function limparErro(campo) {
  campo.classList.remove("is-invalid");
  const proximo = campo.nextElementSibling;
  if (proximo && proximo.classList.contains("invalid-feedback")) {
    proximo.remove();
  }
}

function somenteLetras(valor) {
  return /^[A-Za-zÀ-ÖØ-öø-ÿ\s\-']+$/.test(valor.trim());
}

function apenasNumeroPositivo(valor) {
  return !isNaN(valor) && Number(valor) > 0;
}

function confirmarExclusao() {
  return confirm("Tem certeza que deseja excluir este registro?");
}

/* ── Validação: Continentes ──────────────────────────────── */

function validarContinentes(form) {
  let valido = true;

  const nome = form.querySelector('[name="nome"]');
  const populacao = form.querySelector('[name="populacao"]');
  const area = form.querySelector('[name="area"]');
  const total = form.querySelector('[name="total"]');

  // Nome
  if (!nome.value.trim()) {
    mostrarErro(nome, "O nome do continente é obrigatório.");
    valido = false;
  } else if (!somenteLetras(nome.value)) {
    mostrarErro(nome, "O nome deve conter apenas letras.");
    valido = false;
  } else if (nome.value.trim().length < 3) {
    mostrarErro(nome, "O nome deve ter pelo menos 3 caracteres.");
    valido = false;
  } else {
    limparErro(nome);
  }

  // População
  if (populacao.value !== "" && !apenasNumeroPositivo(populacao.value)) {
    mostrarErro(populacao, "A população deve ser um número positivo.");
    valido = false;
  } else {
    limparErro(populacao);
  }

  // Área
  if (area.value !== "" && !apenasNumeroPositivo(area.value)) {
    mostrarErro(area, "A área deve ser um número positivo.");
    valido = false;
  } else {
    limparErro(area);
  }

  // Total de países
  if (total.value !== "") {
    if (!Number.isInteger(Number(total.value)) || Number(total.value) < 0) {
      mostrarErro(total, "Informe um número inteiro não negativo.");
      valido = false;
    } else {
      limparErro(total);
    }
  }

  return valido;
}

/* ── Validação: Governantes ──────────────────────────────── */

function validarGovernantes(form) {
  let valido = true;

  const nome    = form.querySelector('[name="nome"]');
  const partido = form.querySelector('[name="partido"]');
  const data    = form.querySelector('[name="data"]');
  const idade   = form.querySelector('[name="idade"]');
  const inicio  = form.querySelector('[name="inicio"]');
  const fim     = form.querySelector('[name="fim"]');

  // Nome
  if (!nome.value.trim()) {
    mostrarErro(nome, "O nome do governante é obrigatório.");
    valido = false;
  } else if (!somenteLetras(nome.value)) {
    mostrarErro(nome, "O nome deve conter apenas letras.");
    valido = false;
  } else if (nome.value.trim().length < 3) {
    mostrarErro(nome, "O nome deve ter pelo menos 3 caracteres.");
    valido = false;
  } else {
    limparErro(nome);
  }

  // Partido (opcional, mas se preenchido deve ter letras)
  if (partido.value.trim() && partido.value.trim().length < 2) {
    mostrarErro(partido, "Nome do partido muito curto.");
    valido = false;
  } else {
    limparErro(partido);
  }

  // Data de nascimento
  if (data.value) {
    const nascimento = new Date(data.value);
    const hoje = new Date();
    if (nascimento >= hoje) {
      mostrarErro(data, "A data de nascimento deve ser no passado.");
      valido = false;
    } else {
      limparErro(data);
    }
  }

  // Idade
  if (idade.value !== "") {
    const idadeNum = Number(idade.value);
    if (!Number.isInteger(idadeNum) || idadeNum < 18 || idadeNum > 120) {
      mostrarErro(idade, "Informe uma idade válida (entre 18 e 120 anos).");
      valido = false;
    } else {
      limparErro(idade);
    }
  }

  // Início do mandato
  if (inicio.value && fim.value) {
    if (new Date(fim.value) <= new Date(inicio.value)) {
      mostrarErro(fim, "O fim do mandato deve ser posterior ao início.");
      valido = false;
    } else {
      limparErro(fim);
    }
  }

  return valido;
}

/* ── Validação: Países ───────────────────────────────────── */

function validarPaises(form) {
  let valido = true;

  const nome      = form.querySelector('[name="nome"]');
  const populacao = form.querySelector('[name="populacao"]');
  const area      = form.querySelector('[name="area"]');
  const idioma    = form.querySelector('[name="idioma"]');
  const clima     = form.querySelector('[name="clima"]');
  const regime    = form.querySelector('[name="regime"]');
  const moeda     = form.querySelector('[name="moeda"]');

  // Nome
  if (!nome.value.trim()) {
    mostrarErro(nome, "O nome do país é obrigatório.");
    valido = false;
  } else if (nome.value.trim().length < 2) {
    mostrarErro(nome, "O nome deve ter pelo menos 2 caracteres.");
    valido = false;
  } else {
    limparErro(nome);
  }

  // População
  if (populacao.value !== "" && !apenasNumeroPositivo(populacao.value)) {
    mostrarErro(populacao, "A população deve ser um número positivo.");
    valido = false;
  } else {
    limparErro(populacao);
  }

  // Área
  if (area.value !== "" && !apenasNumeroPositivo(area.value)) {
    mostrarErro(area, "A área deve ser um número positivo.");
    valido = false;
  } else {
    limparErro(area);
  }

  // Idioma
  if (idioma.value.trim() && !somenteLetras(idioma.value)) {
    mostrarErro(idioma, "O idioma deve conter apenas letras.");
    valido = false;
  } else {
    limparErro(idioma);
  }

  // Clima
  if (clima.value.trim() && clima.value.trim().length < 3) {
    mostrarErro(clima, "Descrição de clima muito curta.");
    valido = false;
  } else {
    limparErro(clima);
  }

  // Regime político
  if (regime.value.trim() && regime.value.trim().length < 3) {
    mostrarErro(regime, "Descrição de regime político muito curta.");
    valido = false;
  } else {
    limparErro(regime);
  }

  // Moeda
  if (moeda.value.trim() && !somenteLetras(moeda.value)) {
    mostrarErro(moeda, "O nome da moeda deve conter apenas letras.");
    valido = false;
  } else {
    limparErro(moeda);
  }

  return valido;
}

/* ── Validação: Cidades ──────────────────────────────────── */

function validarCidades(form) {
  let valido = true;

  const nome      = form.querySelector('[name="nome"]');
  const populacao = form.querySelector('[name="populacao"]');
  const area      = form.querySelector('[name="area"]');
  const clima     = form.querySelector('[name="clima"]');
  const data      = form.querySelector('[name="data"]');

  // Nome
  if (!nome.value.trim()) {
    mostrarErro(nome, "O nome da cidade é obrigatório.");
    valido = false;
  } else if (nome.value.trim().length < 2) {
    mostrarErro(nome, "O nome deve ter pelo menos 2 caracteres.");
    valido = false;
  } else {
    limparErro(nome);
  }

  // População
  if (populacao.value !== "" && !apenasNumeroPositivo(populacao.value)) {
    mostrarErro(populacao, "A população deve ser um número positivo.");
    valido = false;
  } else {
    limparErro(populacao);
  }

  // Área
  if (area.value !== "" && !apenasNumeroPositivo(area.value)) {
    mostrarErro(area, "A área deve ser um número positivo.");
    valido = false;
  } else {
    limparErro(area);
  }

  // Clima
  if (clima.value.trim() && clima.value.trim().length < 3) {
    mostrarErro(clima, "Descrição de clima muito curta.");
    valido = false;
  } else {
    limparErro(clima);
  }

  // Data de fundação
  if (data.value) {
    const fundacao = new Date(data.value);
    const hoje = new Date();
    if (fundacao > hoje) {
      mostrarErro(data, "A data de fundação não pode ser futura.");
      valido = false;
    } else {
      limparErro(data);
    }
  }

  return valido;
}

/* ── Inicialização: associa validações aos formulários ───── */

document.addEventListener("DOMContentLoaded", function () {
  const path = window.location.pathname;

  // Identifica a página atual pela URL ou pelo título
  const pagina = path.split("/").pop() || document.title.toLowerCase();

  const forms = document.querySelectorAll("form[method='POST'], form[method='post']");

  forms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      let valido = true;

      if (pagina.includes("continente")) {
        valido = validarContinentes(form);
      } else if (pagina.includes("governante")) {
        valido = validarGovernantes(form);
      } else if (pagina.includes("pais") || pagina.includes("país")) {
        valido = validarPaises(form);
      } else if (pagina.includes("cidade")) {
        valido = validarCidades(form);
      }

      if (!valido) {
        e.preventDefault();
        // Rola até o primeiro erro
        const primeiro = form.querySelector(".is-invalid");
        if (primeiro) primeiro.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });

    // Limpa erros em tempo real ao digitar
    form.querySelectorAll("input, select").forEach(function (campo) {
      campo.addEventListener("input", function () {
        limparErro(campo);
      });
    });
  });
});
