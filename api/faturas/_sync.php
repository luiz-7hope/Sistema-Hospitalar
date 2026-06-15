<?php

declare(strict_types=1);

function limparFaturasDuplicadas(PDO $pdo, ?int $pacienteId = null): void
{
    if ($pacienteId !== null && $pacienteId > 0) {
        $stmt = $pdo->prepare(
            'DELETE f1 FROM faturas f1
             INNER JOIN faturas f2
               ON f1.paciente_id = f2.paciente_id
              AND f1.id < f2.id
             WHERE f1.paciente_id = :paciente_id'
        );
        $stmt->execute(['paciente_id' => $pacienteId]);

        return;
    }

    $pdo->exec(
        'DELETE f1 FROM faturas f1
         INNER JOIN faturas f2
           ON f1.paciente_id = f2.paciente_id
          AND f1.id < f2.id'
    );
}

function sincronizarFaturas(PDO $pdo, ?int $pacienteId = null, ?int $usuarioId = null): void
{
    if ($pacienteId !== null && $pacienteId > 0) {
        $pacienteIds = [$pacienteId];
    } else {
        $stmt = $pdo->query('SELECT id FROM pacientes ORDER BY id ASC');
        $pacienteIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    $totaisStmt = $pdo->prepare(
        'SELECT COALESCE(SUM(preco_total), 0) AS valor_total, MAX(data_uso) AS ultimo_uso
         FROM registros_uso
         WHERE paciente_id = :paciente_id'
    );

    $faturaStmt = $pdo->prepare(
        'SELECT id, status, data_pagamento
         FROM faturas
         WHERE paciente_id = :paciente_id
         LIMIT 1 FOR UPDATE'
    );

    $deleteStmt = $pdo->prepare('DELETE FROM faturas WHERE paciente_id = :paciente_id');

    $upsertStmt = $pdo->prepare(
        'INSERT INTO faturas (paciente_id, valor_total, status, data_emissao, data_pagamento, usuario_id)
         VALUES (:paciente_id, :valor_total, :status, CURRENT_TIMESTAMP, :data_pagamento, :usuario_id)
         ON DUPLICATE KEY UPDATE
            valor_total = VALUES(valor_total),
            status = VALUES(status),
            data_pagamento = VALUES(data_pagamento),
            usuario_id = VALUES(usuario_id)'
    );

    foreach ($pacienteIds as $id) {
        $totaisStmt->execute(['paciente_id' => $id]);
        $totais = $totaisStmt->fetch() ?: ['valor_total' => 0, 'ultimo_uso' => null];

        $valorTotal = (float) $totais['valor_total'];
        $ultimoUso = $totais['ultimo_uso'] !== null ? (string) $totais['ultimo_uso'] : null;

        $faturaStmt->execute(['paciente_id' => $id]);
        $faturaAtual = $faturaStmt->fetch();

        if ($valorTotal <= 0.0) {
            if ($faturaAtual) {
                $deleteStmt->execute(['paciente_id' => $id]);
            }

            continue;
        }

        $status = 'Pendente';
        $dataPagamento = null;

        if ($faturaAtual && ($faturaAtual['status'] ?? '') === 'Pago' && !empty($faturaAtual['data_pagamento'])) {
            $dataPagamentoAtual = (string) $faturaAtual['data_pagamento'];

            if ($ultimoUso !== null && strtotime($ultimoUso) <= strtotime($dataPagamentoAtual)) {
                $status = 'Pago';
                $dataPagamento = $dataPagamentoAtual;
            }
        }

        $upsertStmt->execute([
            'paciente_id' => $id,
            'valor_total' => $valorTotal,
            'status' => $status,
            'data_pagamento' => $dataPagamento,
            'usuario_id' => $usuarioId,
        ]);

        limparFaturasDuplicadas($pdo, (int) $id);
    }
}