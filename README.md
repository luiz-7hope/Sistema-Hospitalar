# 🏥 MediCare - Sistema de Gerenciamento Hospitalar

**Sistema web completo para gerenciamento de pacientes, medicamentos e faturas em ambientes hospitalares.**

---

## 📋 Sobre o Projeto

O **MediCare** é um sistema de gerenciamento hospitalar desenvolvido com **HTML, CSS e JavaScript puro**, utilizando `localStorage` para persistência de dados. O sistema oferece uma interface intuitiva com tema cyberpunk dark para facilitar o uso em ambientes hospitalares.

### 🎯 Para que serve?

O MediCare serve para:
- ✅ Gerenciar registros de pacientes internados
- ✅ Controlar estoque de medicamentos
- ✅ Registrar uso de medicamentos por paciente
- ✅ Gerar faturas e cobranças automáticas
- ✅ Visualizar dashboard com estatísticas em tempo real
- ✅ Manter segurança com sistema de autenticação

---

## 🔧 Funcionalidades Principais

### 1. **🔐 Sistema de Autenticação**
- Login com validação de usuário e senha
- Criação de novos usuários (signup)
- Sessão persistente com `localStorage`
- Logout com confirmação
- Redirecionamento automático para não autenticados

### 2. **📊 Dashboard**
- Visualização de estatísticas principais:
  - Total de pacientes internados
  - Total de medicamentos em estoque
  - Total de registros de uso
  - Valor total de cobranças pendentes
- Listagem dos últimos registros de uso
- Informações do usuário logado

### 3. **👥 Gerenciamento de Pacientes**
- Adicionar novo paciente com dados:
  - Nome completo
  - CPF
  - Plano de saúde
  - Número do leito
  - Data de internação
  - Status (Internado, Alta Médica, Crítico)
- Visualizar lista completa de pacientes
- Editar dados de pacientes
- Deletar registros de pacientes
- Tabela com filtros e ordenação automática

### 4. **💊 Gerenciamento de Medicamentos**
- Cadastro de medicamentos com:
  - Nome do medicamento
  - Código/Registro (ANVISA)
  - Categoria (Analgésico, Antibiótico, Anti-inflamatório, etc.)
  - Preço unitário
  - Quantidade em estoque
- Controle automático de estoque
- Alertas de estoque baixo (< 10 unidades)
- Atualização em tempo real do estoque

### 5. **📝 Registro de Uso de Medicamentos**
- Registrar uso de medicamento por paciente:
  - Seleção de paciente
  - Seleção de medicamento
  - Quantidade utilizada
  - Data e hora do uso
  - Observações
- Desconto automático do estoque
- Cálculo automático do valor (quantidade × preço)
- Histórico completo de uso

### 6. **💰 Faturas e Cobranças**
- Geração automática de faturas por paciente
- Status: Pendente (internado) ou Pago (alta)
- Filtro por paciente
- Filtro por status de pagamento
- Listagem de medicamentos utilizados por fatura
- Total de cobrança por paciente
- Opção de imprimir fatura
- Marcar fatura como paga

### 7. **🎨 Interface Cyberpunk Dark Theme**
- Design moderno e intuitivo
- Tema escuro para reduzir cansaço visual
- Cores neon (verde, rosa, ciano, roxo)
- Animações suaves
- Responsivo em diferentes tamanhos de tela
- Notificações em tempo real (toast)
- Modal de confirmação

---

## 📦 O que o Sistema Utiliza

### **Frontend:**
- **HTML5** - Estrutura semântica
- **CSS3** - Styling com variáveis CSS, gradientes e animações
- **JavaScript (Vanilla)** - Sem dependências externas
- **LocalStorage** - Persistência de dados no navegador

### **Tecnologias:**
- ✅ DOM Manipulation
- ✅ Event Listeners
- ✅ Local Storage API
- ✅ Date & Time Handling
- ✅ String Formatting (Português BR)
- ✅ Array Methods (map, filter, find, reduce)

---

## 🚀 Como Usar

### 1. **Abrir o Sistema**
```bash
# Abra o arquivo login.html no navegador
# Ou use Live Server (VS Code)
```

### 2. **Fazer Login**
- Digite qualquer usuário e senha
- Clique em "Login" ou "Criar Conta"
- Será redirecionado ao dashboard

### 3. **Navegar pelas Abas**
- 📊 **Dashboard** - Visualizar estatísticas
- 👥 **Pacientes** - Gerenciar pacientes
- 💊 **Medicamentos** - Gerenciar medicamentos
- 📝 **Registro** - Registrar uso de medicamentos
- 💰 **Faturas** - Visualizar cobranças

### 4. **Adicionar Dados**
Preencha os formulários e clique nos botões:
- ➕ **Adicionar Paciente**
- ➕ **Adicionar Medicamento**
- 📝 **Registrar Uso**

### 5. **Gerenciar Dados**
- **Editar** - Modifique informações
- **Deletar** - Remova registros
- **Imprimir** - Gere faturas em PDF
- **Marcar Pago** - Atualize status de fatura

---

## 📂 Arquivos do Projeto

```
Trabalho Quarta/
├── index.html          # Dashboard e sistema principal
├── login.html          # Tela de autenticação
├── signup.html         # Tela de criação de conta
├── style.css           # Estilos e tema cyberpunk
└── README.md           # Este arquivo
```

---

## 💾 Dados Armazenados

Todos os dados são salvos no **localStorage** do navegador:

```javascript
{
  "usuarioLogado": {
    "nome": "Nome do Usuário",
    "senha": "senha"
  },
  "medicareDados": {
    "pacientes": [...],
    "medicamentos": [...],
    "registros": [...]
  }
}
```

⚠️ **Nota:** Os dados são locais. Limpar cache/cookies do navegador apagará tudo.

---

## 🎨 Paleta de Cores

| Cor | Uso | Código |
|-----|-----|--------|
| Verde Neon | Primária/Botões | `#00ff41` |
| Rosa Magenta | Secundária | `#ff006e` |
| Ciano | Terciária/Bordas | `#00d9ff` |
| Roxo | Accent | `#b537f2` |
| Fundo Escuro | Background | `#0a0e27` |

---

## 📋 Requisitos

- ✅ Navegador moderno (Chrome, Firefox, Safari, Edge)
- ✅ JavaScript habilitado
- ✅ LocalStorage habilitado
- ✅ Sem dependências externas

---

## 🔒 Segurança

⚠️ **Informações importantes:**
- Sistema usa `localStorage` - NÃO é seguro para produção
- Senhas não são criptografadas
- Para usar em produção, implementar:
  - Backend com autenticação JWT
  - Banco de dados (MySQL, PostgreSQL, MongoDB)
  - Criptografia de dados
  - HTTPS

---

## 🐛 Recursos Futuros

- [ ] Backend com Node.js/Express
- [ ] Banco de dados relacional
- [ ] Autenticação com JWT
- [ ] Multi-usuários com permissões
- [ ] Exportar relatórios em PDF
- [ ] Integração com farmácia
- [ ] Notificações por email
- [ ] Backup automático

---

## 👨‍💻 Desenvolvido por

**SENAC DF - Trabalho Quarta**

---

## 📄 Licença

Projeto educacional. Uso livre para fins acadêmicos.

---

## 📞 Suporte

Para dúvidas sobre funcionamento do sistema, revise as seções acima ou entre em contato com o desenvolvedor.