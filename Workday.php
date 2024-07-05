<?php

declare(strict_types=1);

require_once 'DB.php';

class Workday
{
    const START_WORK = '09:00:00';
    const END_WORK = '18:00:00';
    const TOTAL_WORK_TIME = 9;

    public PDO $pdo;

    public function __construct()
    {
        date_default_timezone_set('Asia/Tashkent');
        $this->pdo = DB::connect();
    }

    public function totalReport(array $_post): array
    {
        if (!empty($_post['arrived_at']) && !empty($_post['leaved_at'])) {
            try {
                $arrived_at = new DateTime($_post['arrived_at']);
                $leaved_at = new DateTime($_post['leaved_at']);
                $required_work_off = $this->calculateWorkOff($arrived_at, $leaved_at);
            } catch (Exception $e) {
                return [
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ];
            }

            $query = "INSERT INTO daily (arrived_at, leaved_at, required_work_off, worked_off)
                      VALUES (:arrived_at, :leaved_at, :required_work_off, false)";
            $arrived_at = $arrived_at->format('Y-m-d H:i:s');
            $leaved_at = $leaved_at->format('Y-m-d H:i:s');

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':arrived_at', $arrived_at);
            $stmt->bindParam(':leaved_at', $leaved_at);
            $stmt->bindParam(':required_work_off', $required_work_off);
            $stmt->execute();

            return [
                'status' => 'success',
                'message' => 'Details added successfully'
            ];
        } else {
            return [
                'status' => 'failed',
                'message' => 'Fill in the blanks'
            ];
        }
    }

    public function getWorkDayList(): array
    {
        return $this->pdo->query("SELECT * FROM daily")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateWorkOff(DateTime $arrivedAt, DateTime $leavedAt): int
    {
        $workTimeInterval = $leavedAt->diff($arrivedAt);
        $workHoursDurationInSeconds = self::TOTAL_WORK_TIME * 60 * 60;

        $workedInSeconds = $workTimeInterval->h * 3600 + $workTimeInterval->i * 60 + $workTimeInterval->s;

        return $workHoursDurationInSeconds - $workedInSeconds;
    }

    public function getTotalWorkOffTime(): string
    {
        $totalWorkOffInSeconds = (int)$this->pdo
            ->query("SELECT SUM(CAST(required_work_off AS DECIMAL(10))) as total_sum FROM daily")
            ->fetch()['total_sum'];

        return $this->getHumanReadableDiff($totalWorkOffInSeconds);
    }

    public function getHumanReadableDiff(int $seconds): string
    {
        $minutes = floor($seconds / 60);

        if ($minutes < 60) {
            return "$minutes min.";
        } else {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;

            $result = "$hours hours";
            if ($remainingMinutes > 0) {
                $result .= " and $remainingMinutes min.";
            }

            return $result;
        }
    }

    public function update(int $daily_id): array
    {
        $sql = "UPDATE daily SET worked_off = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $daily_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Work off updated successfully'
            ];
        } else {
            return [
                'status' => 'failed',
                'message' => 'Failed to update work off'
            ];
        }
    }
}
?>