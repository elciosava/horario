<?php 
    include 'conexao/conexao.php'; 
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamento de Aulas - SENAI União da Vitória</title>
    <link rel="icon" href="img/senai.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    </style>
</head>

<body>
    <header>
        <h2>📅 Agendamento de Aulas - SENAI União da Vitória</h2><a href="api/cadastro_aula.php">Cadastrar aulas</a>
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

                // Monta a tabela
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

                        ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'].forEach(dia => {
                            const td = document.createElement('td');
                            const aula = professores[nome][turno][dia];

                            if (aula && aula.descricao) {
                                td.textContent = aula.sigla || aula.descricao;
                                td.style.backgroundColor = aula.cor;
                                td.style.color = '#fff';
                            } else {
                                td.textContent = '+';
                                td.classList.add('vazio');
                                td.addEventListener('click', () => abrirModal(nome, turno, dia));
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

        // 💾 Salvar o agendamento de aula
        document.getElementById('formAula').addEventListener('submit', async (e) => {
            e.preventDefault();

            // 🧮 Calcula a segunda-feira da semana exibida
            const segunda = obterSegunda(dataInicioSemana);
            const semanaInicio = segunda.toISOString().split('T')[0];

            // 🧩 Monta os dados para enviar ao PHP
            const dados = {
                tipo_aula_id: document.getElementById('aula_select').value,
                dia_semana: document.getElementById('dia_semana').value,
                turno: document.getElementById('turno').value,
                semana_inicio: semanaInicio // envia a semana exibida
            };

            // 📤 Envia para o backend
            const res = await fetch('api/gravar_aula.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });

            const resp = await res.json();

            if (resp.status === 'ok') {
                document.getElementById('modal').style.display = 'none';
                carregarAgenda(); // recarrega tabela da semana atual
            } else {
                alert('Erro: ' + resp.mensagem);
            }
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