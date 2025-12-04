
const mockEmployees = [
  { id:'1', name:'Ana Silva', email:'ana.silva@empresa.com', position:'Desenvolvedora Senior', department:'Tecnologia', performanceScore:92 },
  { id:'2', name:'Carlos Santos', email:'carlos.santos@empresa.com', position:'Gerente de Projetos', department:'Operações', performanceScore:88 },
  { id:'3', name:'Maria Oliveira', email:'maria.oliveira@empresa.com', position:'Designer UX', department:'Design', performanceScore:85 },
  { id:'4', name:'Pedro Costa', email:'pedro.costa@empresa.com', position:'Analista de RH', department:'Recursos Humanos', performanceScore:78 },
  { id:'5', name:'Julia Ferreira', email:'julia.ferreira@empresa.com', position:'Coordenadora de Marketing', department:'Marketing', performanceScore:95 },
];

const mockGoals = [
  { id:'1', employeeId:'1', progress:75, status:'in_progress' },
  { id:'2', employeeId:'1', progress:100, status:'completed' },
  { id:'3', employeeId:'2', progress:60, status:'in_progress' },
  { id:'4', employeeId:'3', progress:40, status:'in_progress' },
  { id:'5', employeeId:'5', progress:100, status:'completed' },
];

const mockEvaluations = [
  { id:'1', employeeId:'1', type:'quarterly', score:92, date:'2024-01-15', evaluatorName:'Roberto Mendes' },
  { id:'2', employeeId:'2', type:'quarterly', score:88, date:'2024-01-20', evaluatorName:'Patricia Alves' },
  { id:'3', employeeId:'3', type:'monthly', score:85, date:'2024-02-01', evaluatorName:'Roberto Mendes' },
  { id:'4', employeeId:'5', type:'quarterly', score:95, date:'2024-01-25', evaluatorName:'Patricia Alves' },
];

const mockFeedbacks = [
  { id:'1', employeeId:'1', authorName:'Carlos Santos', content:'Ana demonstrou excelente habilidade de mentoria', date:'2024-02-05' },
  { id:'2', employeeId:'2', authorName:'Ana Silva', content:'Carlos poderia melhorar a clareza nas reuniões', date:'2024-02-03' },
  { id:'3', employeeId:'3', authorName:'Julia Ferreira', content:'O novo design da landing page ficou incrível!', date:'2024-02-07' },
  { id:'4', employeeId:'5', authorName:'Pedro Costa', content:'Julia organizou um excelente evento de team building.', date:'2024-02-10' },
];

const mockDashboardStats = { totalEmployees:5, averagePerformance:87.6, goalsCompleted:2, pendingEvaluations:3 };

const performanceHistory = [
  { month:'Set', score:82 }, { month:'Out', score:85 }, { month:'Nov', score:84 },
  { month:'Dez', score:88 }, { month:'Jan', score:87 }, { month:'Fev', score:91 }
];

const departmentPerformance = [
  { name:'Tecnologia', value:90 },
  { name:'Design', value:85 },
  { name:'Marketing', value:95 },
  { name:'RH', value:78 },
  { name:'Operações', value:88 },
];


function $(sel){return document.querySelector(sel)}
function $all(sel){return Array.from(document.querySelectorAll(sel))}

function renderStats(){
  $('#totalEmployees').textContent = mockDashboardStats.totalEmployees;
  $('#avgPerformance').textContent = mockDashboardStats.averagePerformance + '%';
  $('#goalsCompleted').textContent = mockDashboardStats.goalsCompleted;
  $('#pendingEvaluations').textContent = mockDashboardStats.pendingEvaluations;
}

function drawLineChart(containerId, data){
  const container = document.getElementById(containerId);
  container.innerHTML = '';
  const w = container.clientWidth || 600;
  const h = 300;
  const padding = {left:40, right:20, top:20, bottom:30};
  const innerW = w - padding.left - padding.right;
  const innerH = h - padding.top - padding.bottom;

  const scores = data.map(d => d.score);
  const minY = Math.min(70, Math.floor(Math.min(...scores)/5)*5);
  const maxY = Math.max(100, Math.ceil(Math.max(...scores)/5)*5);

  const svgNS = "http://www.w3.org/2000/svg";
  const svg = document.createElementNS(svgNS,'svg');
  svg.setAttribute('width','100%');
  svg.setAttribute('height', h);
  svg.setAttribute('viewBox', `0 0 ${w} ${h}`);

  for(let i=0;i<=4;i++){
    const y = padding.top + (innerH)*(i/4);
    const line = document.createElementNS(svgNS,'line');
    line.setAttribute('x1', padding.left);
    line.setAttribute('x2', padding.left+innerW);
    line.setAttribute('y1', y);
    line.setAttribute('y2', y);
    line.setAttribute('stroke', '#e6eef3');
    line.setAttribute('stroke-dasharray','3 4');
    svg.appendChild(line);
  }

  for(let i=0;i<=4;i++){
    const value = Math.round(maxY - ( (maxY-minY)*(i/4) ));
    const y = padding.top + innerH*(i/4);
    const text = document.createElementNS(svgNS,'text');
    text.setAttribute('x', 8);
    text.setAttribute('y', y+4);
    text.setAttribute('font-size','11');
    text.setAttribute('fill','#6b7280');
    text.textContent = value;
    svg.appendChild(text);
  }

  const points = data.map((d, i) => {
    const x = padding.left + (innerW)*(i/(data.length-1));
    const y = padding.top + innerH*(1 - ( (d.score - minY) / (maxY - minY) ));
    return {x,y};
  });

  const area = document.createElementNS(svgNS,'path');
  let dArea = `M ${points[0].x} ${points[0].y} `;
  for(let i=1;i<points.length;i++){
    dArea += `L ${points[i].x} ${points[i].y} `;
  }
  dArea += `L ${points[points.length-1].x} ${padding.top+innerH} L ${points[0].x} ${padding.top+innerH} Z`;
  area.setAttribute('d', dArea);
  area.setAttribute('fill', 'rgba(0,122,168,0.06)');
  svg.appendChild(area);

  const path = document.createElementNS(svgNS,'path');
  let dPath = `M ${points[0].x} ${points[0].y} `;
  for(let i=1;i<points.length;i++){
    dPath += `L ${points[i].x} ${points[i].y} `;
  }
  path.setAttribute('d', dPath);
  path.setAttribute('fill','none');
  path.setAttribute('stroke','#0f6b93');
  path.setAttribute('stroke-width','2.5');
  svg.appendChild(path);

  points.forEach(pt=>{
    const c = document.createElementNS(svgNS,'circle');
    c.setAttribute('cx', pt.x);
    c.setAttribute('cy', pt.y);
    c.setAttribute('r','4.2');
    c.setAttribute('fill','#0f6b93');
    c.setAttribute('stroke','#fff');
    c.setAttribute('stroke-width','1');
    svg.appendChild(c);
  });

  data.forEach((d,i)=>{
    const x = padding.left + (innerW)*(i/(data.length-1));
    const text = document.createElementNS(svgNS,'text');
    text.setAttribute('x', x);
    text.setAttribute('y', padding.top + innerH + 20);
    text.setAttribute('font-size','12');
    text.setAttribute('fill','#6b7280');
    text.setAttribute('text-anchor','middle');
    text.textContent = d.month;
    svg.appendChild(text);
  });

  container.appendChild(svg);
}

function drawBarChart(containerId, data){
  const container = document.getElementById(containerId);
  container.innerHTML = '';
  const w = container.clientWidth || 600;
  const h = 300;
  const padding = {left:120, right:20, top:20, bottom:20};
  const innerW = w - padding.left - padding.right;
  const innerH = h - padding.top - padding.bottom;
  const svgNS = "http://www.w3.org/2000/svg";
  const svg = document.createElementNS(svgNS,'svg');
  svg.setAttribute('width','100%');
  svg.setAttribute('height',h);
  svg.setAttribute('viewBox', `0 0 ${w} ${h}`);

  const max = 100;

  const rowH = innerH / data.length;
  data.forEach((d,i)=>{
    const y = padding.top + i*rowH + 6;
    const barW = (d.value / max) * innerW;

    const label = document.createElementNS(svgNS,'text');
    label.setAttribute('x', 10);
    label.setAttribute('y', y+14);
    label.setAttribute('font-size','13');
    label.setAttribute('fill','#6b7280');
    label.textContent = d.name;
    svg.appendChild(label);

    const bg = document.createElementNS(svgNS,'rect');
    bg.setAttribute('x', padding.left);
    bg.setAttribute('y', y);
    bg.setAttribute('width', innerW);
    bg.setAttribute('height', 18);
    bg.setAttribute('rx', 9);
    bg.setAttribute('fill', '#eef6fa');
    svg.appendChild(bg);

    const color = pickColor(i);
    const bar = document.createElementNS(svgNS,'rect');
    bar.setAttribute('x', padding.left);
    bar.setAttribute('y', y);
    bar.setAttribute('width', barW);
    bar.setAttribute('height', 18);
    bar.setAttribute('rx', 9);
    bar.setAttribute('fill', color);
    svg.appendChild(bar);

    const val = document.createElementNS(svgNS,'text');
    val.setAttribute('x', padding.left + Math.min(barW + 8, innerW - 10));
    val.setAttribute('y', y + 14);
    val.setAttribute('font-size','12');
    val.setAttribute('fill','#0f1724');
    val.textContent = d.value;
    svg.appendChild(val);
  });

  container.appendChild(svg);
}

function pickColor(i){
  const palette = ['#0f86b2','#18b18b','#f6a623','#a86ddb','#ff5a7a'];
  return palette[i % palette.length];
}

function renderActivities(){
  const container = document.getElementById('activitiesList');
  container.innerHTML = '';
  const activities = [
    ...mockEvaluations.map(e => ({...e, kind:'evaluation'})),
    ...mockFeedbacks.map(f => ({...f, kind:'feedback'}))
  ].sort((a,b)=> new Date(b.date) - new Date(a.date)).slice(0,6);

  activities.forEach(act=>{
    const el = document.createElement('div');
    el.className = 'recent-item';
    el.innerHTML = `
      <div class="icon">${act.kind==='evaluation'?'🗂':'💬'}</div>
      <div class="content">
        <div style="font-weight:600">${act.kind==='evaluation' ? 'Avaliação' : 'Novo Feedback'}</div>
        <div class="meta">${act.kind==='evaluation' ? `Score: ${act.score}` : act.content}</div>
      </div>
      <div style="font-size:12px;color:#6b7280">${formatDate(act.date)}</div>
    `;
    container.appendChild(el);
  });
}

function renderPerformers(){
  const container = document.getElementById('performersList');
  container.innerHTML = '';
  const top = [...mockEmployees].sort((a,b)=>b.performanceScore - a.performanceScore).slice(0,5);
  top.forEach((p,i)=>{
    const el = document.createElement('div');
    el.className = 'performer';
    el.innerHTML = `
      <div class="rank">${i+1}</div>
      <div class="avatar">${p.name.split(' ').map(n=>n[0]).slice(0,2).join('')}</div>
      <div style="flex:1">
        <div style="font-weight:600">${p.name}</div>
        <div style="font-size:12px;color:#6b7280">${p.department || p.position}</div>
      </div>
      <div style="text-align:right">
        <div style="font-weight:700;color:#0f1724">${p.performanceScore}</div>
        <div style="font-size:12px;color:#6b7280">pts</div>
      </div>
    `;
    container.appendChild(el);
  });
}

function formatDate(d){
  const dt = new Date(d);
  return dt.toLocaleDateString('pt-BR', { day:'2-digit', month:'short' });
}

function populateUser(){
  try {
    const user = JSON.parse(localStorage.getItem('user') || 'null');
    if(user && user.name){
      $('#userName').textContent = user.name;
      $('#userEmail').textContent = user.email;
      $('.avatar') && ($('.avatar').textContent = (user.name[0] || 'U').toUpperCase());
    }
  } catch(err){}
}

function setupLogout(){
  const btn = document.getElementById('logoutBtn');
  btn.addEventListener('click', ()=>{
    localStorage.removeItem('user');
    window.location.href = '../index.html';
  });
}

function init(){
  renderStats();
  drawLineChart('lineChart', performanceHistory);
  drawBarChart('barChart', departmentPerformance);
  renderActivities();
  renderPerformers();
  populateUser();
  setupLogout();
}

window.addEventListener('load', init);

let resizeTimer;
window.addEventListener('resize', ()=>{
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(()=> {
    drawLineChart('lineChart', performanceHistory);
    drawBarChart('barChart', departmentPerformance);
  }, 200);
});
