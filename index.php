<?php
include 'conexao/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamento de Aulas - SENAI União da Vitória</title>
    <link rel="icon" href="img/senai.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <link rel="stylesheet" href="css/style.css">
    </style>
</head>

<body>
    <header>
        <h2>📅 Agendamento de Aulas - SENAI União da Vitória</h2><div class="cadastra"><a href="api/cadastro_professor.php">Cadastrar professor</a><a href="api/cadastro_aula.php">Cadastrar aulas</a></div>
    </header>

    <main>
        <div class="semana-controles">
            <button id="semana-anterior">◀️ Semana anterior</button>
            <span id="semana-atual"></span>
            <button id="semana-proxima">Semana seguinte ▶️</button>
        </div>

        <table id="tabela-agenda">
            <thead>
                <tr>
                    <th>Professor</th>
                    <th>Turno</th>
                    <th>Seg</th>
                    <th>Ter</th>
                    <th>Qua</th>
                    <th>Qui</th>
                    <th>Sex</th>
                    <th>Sab</th>
                    <th>Dom</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <!-- Preenchido via JS -->
            </tbody>
        </table>
        <div class="exportar">
            <button id="btn-exportar-pdf"
                style="margin:20px auto;display:block;background:#c3002f;color:#fff;border:0;padding:10px 20px;border-radius:5px;cursor:pointer;">
                📄 Exportar semana em PDF
            </button>
            <button id="btn-exportar-img"
                style="margin:10px auto;display:block;background:#1a2041;color:#fff;border:0;padding:10px 20px;border-radius:5px;cursor:pointer;">
                🖼️ Exportar semana como imagem
            </button>
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h2>Agendar Aula</h2>
            <form id="formAula">
                <label>Professor:</label>
                <select id="professor_select"></select>

                <label>Aula:</label>
                <select id="aula_select">
                    <option value="">Selecione o professor primeiro</option>
                </select>

                <label>Dia da Semana:</label>
                <select id="dia_semana">
                    <option>Seg</option>
                    <option>Ter</option>
                    <option>Qua</option>
                    <option>Qui</option>
                    <option>Sex</option>
                    <option>Sab</option>
                    <option>Dom</option>
                </select>

                <label>Turno:</label>
                <select id="turno">
                    <option>Manhã</option>
                    <option>Tarde</option>
                    <option>Noite</option>
                </select>

                <div class="botoes">
                    <button type="submit">Salvar</button>
                    <button type="button" id="cancelar">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let dataInicioSemana = new Date(); // começa na data atual

        // Função que retorna a segunda-feira da semana atual
        function obterSegunda(d) {
            const data = new Date(d);
            const dia = data.getDay(); // 0 = domingo, 1 = segunda...
            const diff = data.getDate() - dia + (dia === 0 ? -6 : 1);
            return new Date(data.setDate(diff));
        }

        // Atualiza o texto no topo com a semana exibida
        function atualizarSemanaTexto() {
            const segunda = obterSegunda(dataInicioSemana);
            const domingo = new Date(segunda);
            domingo.setDate(segunda.getDate() + 6);

            const fmt = (data) => data.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            document.getElementById('semana-atual').textContent =
                `${fmt(segunda)} até ${fmt(domingo)}`;
        }

        // Quando clicar nos botões
        document.getElementById('semana-anterior').addEventListener('click', () => {
            dataInicioSemana.setDate(dataInicioSemana.getDate() - 7);
            atualizarSemanaTexto();
            carregarAgenda();
        });

        document.getElementById('semana-proxima').addEventListener('click', () => {
            dataInicioSemana.setDate(dataInicioSemana.getDate() + 7);
            atualizarSemanaTexto();
            carregarAgenda();
        });

        async function carregarAgenda() {
    const corpo = document.getElementById('corpo-tabela');
    corpo.innerHTML = '';

    try {
        // 🔹 Calcula a segunda-feira da semana exibida
        const segunda = obterSegunda(dataInicioSemana);
        const semanaInicio = segunda.toISOString().split('T')[0]; // formato YYYY-MM-DD

        // 🔹 Busca professores e aulas da semana atual
        const [profResp, aulaResp] = await Promise.all([
            fetch('api/listar_professores.php').then(r => r.json()),
            fetch(`api/listar_aulas.php?semana_inicio=${semanaInicio}`).then(r => r.json())
        ]);

        console.log('📘 Professores recebidos:', profResp);
        console.log('📅 Aulas recebidas:', aulaResp);

        // Cria estrutura base
        const professores = {};
        profResp.forEach(p => {
            professores[p.nome] = { Manhã: {}, Tarde: {}, Noite: {} };
        });

        // Adiciona aulas existentes (se houver)
        aulaResp.forEach(aula => {
            if (!professores[aula.professor]) {
                professores[aula.professor] = { Manhã: {}, Tarde: {}, Noite: {} };
            }
            professores[aula.professor][aula.turno][aula.dia_semana] = aula;
        });

        // 🔹 Gera dias da semana com data
        const diasSemana = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'];
        const diasComDatas = diasSemana.map((sigla, i) => {
            const data = new Date(segunda);
            data.setDate(segunda.getDate() + i);
            const diaMes = data.getDate().toString().padStart(2, '0');
            const mes = (data.getMonth() + 1).toString().padStart(2, '0');
            return { sigla, data, label: `${sigla} ${diaMes}/${mes}` };
        });

        // 🔹 Atualiza o cabeçalho com dia + data
        const theadRow = document.querySelector('#tabela-agenda thead tr');
        theadRow.innerHTML = `
            <th>Professor</th>
            <th>Turno</th>
            ${diasComDatas.map(d => `<th>${d.label}</th>`).join('')}
        `;

        // 🔹 Dia atual (para destaque visual)
        const hoje = new Date();
        const hojeStr = hoje.toISOString().split('T')[0];

        // 🔹 Monta a tabela
        for (const nome in professores) {
            ['Manhã', 'Tarde', 'Noite'].forEach(turno => {
                const linha = document.createElement('tr');

                if (turno === 'Manhã') {
                    const tdNome = document.createElement('td');
                    tdNome.rowSpan = 3;
                    tdNome.textContent = nome;
                    linha.appendChild(tdNome);
                }

                const tdTurno = document.createElement('td');
                tdTurno.textContent = turno;
                linha.appendChild(tdTurno);

                diasComDatas.forEach(d => {
                    const td = document.createElement('td');
                    const aula = professores[nome][turno][d.sigla];

                    // 🔸 Destaca o dia atual com um leve traço azul SENAI
                    if (d.data.toISOString().split('T')[0] === hojeStr) {
                        td.style.borderBottom = '3px solid #007bff';
                    }

                    if (aula && aula.descricao) {
                        td.innerHTML = `
                            <div class="aula" style="background:${aula.cor};color:#fff;">
                                <span>${aula.sigla || aula.descricao}</span>
                                <div class="acoes">
                                    <button class="editar" title="Editar">✏️</button>
                                    <button class="excluir" title="Excluir">🗑️</button>
                                </div>
                            </div>`;
                        td.querySelector('.editar').addEventListener('click', () => editarAula(aula.id));
                        td.querySelector('.excluir').addEventListener('click', () => excluirAula(aula.id));
                    } else {
                        td.textContent = '+';
                        td.classList.add('vazio');
                        td.addEventListener('click', () => abrirModal(nome, turno, d.sigla));
                    }

                    linha.appendChild(td);
                });

                corpo.appendChild(linha);
            });
        }

    } catch (erro) {
        console.error('Erro ao carregar:', erro);
    }
}




        // 🧠 Abre o modal e preenche dados básicos
        function abrirModal(professor, turno, dia) {
            document.getElementById('modal').style.display = 'flex';
            document.getElementById('dia_semana').value = dia;
            document.getElementById('turno').value = turno;

            // Seleciona o professor no select
            const selectProf = document.getElementById('professor_select');
            const opt = Array.from(selectProf.options).find(o => o.text === professor);
            if (opt) selectProf.value = opt.value;
            selectProf.dispatchEvent(new Event('change'));
        }

        // Fecha o modal
        document.getElementById('cancelar').addEventListener('click', () => {
            document.getElementById('modal').style.display = 'none';
        });

        // 🧩 Carregar professores no select do modal
        async function carregarProfessores() {
            const selectProfessor = document.getElementById('professor_select');
            const res = await fetch('api/listar_professores.php');
            const profs = await res.json();

            selectProfessor.innerHTML =
                '<option value="">Selecione...</option>' +
                profs.map(p => `<option value="${p.id}">${p.nome}</option>`).join('');
        }

        // 🧩 Atualizar aulas ao trocar o professor
        const selectProfessor = document.getElementById('professor_select');
        const selectAula = document.getElementById('aula_select');

        selectProfessor.addEventListener('change', async () => {
            const id = selectProfessor.value;
            selectAula.innerHTML = '<option>Carregando...</option>';

            const res = await fetch(`api/listar_tipos_aula.php?professor_id=${id}`);
            const aulas = await res.json();

            if (aulas.length > 0) {
                selectAula.innerHTML = aulas
                    .map(a => `<option value="${a.id}">${a.sigla} - ${a.descricao}</option>`)
                    .join('');
            } else {
                selectAula.innerHTML = '<option>Nenhuma aula cadastrada</option>';
            }
        });

        // 💾 Salvar ou editar o agendamento de aula
        document.getElementById('formAula').addEventListener('submit', async (e) => {
            e.preventDefault();

            // 🧮 Calcula a segunda-feira da semana exibida
            const segunda = obterSegunda(dataInicioSemana);
            const semanaInicio = segunda.toISOString().split('T')[0];

            // 🧩 Verifica se está editando (dataset.editando é setado pelo editarAula)
            const idEditando = document.getElementById('formAula').dataset.editando || null;

            // Monta os dados a serem enviados
            const dados = {
                id: idEditando,
                tipo_aula_id: document.getElementById('aula_select').value,
                dia_semana: document.getElementById('dia_semana').value,
                turno: document.getElementById('turno').value,
                semana_inicio: semanaInicio
            };

            // Define o endpoint conforme a ação
            const endpoint = idEditando ? 'api/editar_aula.php' : 'api/gravar_aula.php';

            // 📤 Envia os dados ao backend
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });

            const resp = await res.json();

            if (resp.status === 'ok') {
                document.getElementById('modal').style.display = 'none';
                delete document.getElementById('formAula').dataset.editando; // limpa modo edição
                document.querySelector('#modal h2').textContent = 'Agendar Aula'; // volta título padrão
                carregarAgenda(); // recarrega tabela
            } else {
                alert('Erro: ' + resp.mensagem);
            }
        });


        // ✏️ Editar aula
        function editarAula(id) {
            fetch(`api/obter_aula.php?id=${id}`)
                .then(r => r.json())
                .then(aula => {
                    document.getElementById('modal').style.display = 'flex';
                    document.getElementById('professor_select').value = aula.professor_id;
                    document.getElementById('professor_select').dispatchEvent(new Event('change'));

                    // aguarda o select de aulas carregar antes de selecionar a certa
                    setTimeout(() => {
                        document.getElementById('aula_select').value = aula.tipo_aula_id;
                    }, 300);

                    document.getElementById('dia_semana').value = aula.dia_semana;
                    document.getElementById('turno').value = aula.turno;

                    // marca que o modal está em modo de edição
                    document.getElementById('formAula').dataset.editando = id;
                    document.querySelector('#modal h2').textContent = 'Editar Aula';
                })
                .catch(err => console.error('Erro ao obter aula:', err));
        }

        // 🗑️ Excluir aula
        async function excluirAula(id) {
            if (!confirm('Tem certeza que deseja excluir esta aula?')) return;

            const res = await fetch('api/excluir_aula.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            const resp = await res.json();

            if (resp.status === 'ok') {
                carregarAgenda();
            } else {
                alert('Erro: ' + resp.mensagem);
            }
        }

        // 📤 Exportar tabela como PDF
        document.getElementById('btn-exportar-pdf').addEventListener('click', async () => {
            const tabela = document.querySelector('#tabela-agenda');
            const semanaTexto = document.querySelector('#semana-atual').textContent.trim();

            // Cria imagem da tabela
            const canvas = await html2canvas(tabela, { scale: 2 });
            const imgData = canvas.toDataURL('image/png');

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('l', 'pt', 'a4'); // paisagem
            const imgWidth = pdf.internal.pageSize.getWidth();
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            pdf.text(`Agenda Semanal (${semanaTexto})`, 40, 40);
            pdf.addImage(imgData, 'PNG', 30, 60, imgWidth - 60, imgHeight);
            pdf.save(`Agenda_${semanaTexto.replace(/\s/g, '_')}.pdf`);
        });

        // 📸 Exportar tabela como imagem
        document.getElementById('btn-exportar-img').addEventListener('click', async () => {
            const tabela = document.querySelector('#tabela-agenda');
            const semanaTexto = document.querySelector('#semana-atual').textContent.trim();

            const canvas = await html2canvas(tabela, { scale: 2 });
            const link = document.createElement('a');
            link.download = `Agenda_${semanaTexto.replace(/\s/g, '_')}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        });




        // 🚀 Quando a página carregar, inicializa tudo
        document.addEventListener('DOMContentLoaded', () => {
            atualizarSemanaTexto(); // 🧠 Adiciona esta linha!
            carregarAgenda();
            carregarProfessores();
        });

    </script>

</body>

</html>