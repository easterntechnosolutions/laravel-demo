<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportCsvLogsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'file_path' =>$this->file_path,
            'model_name' => $this->model_name,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'status_text' => config('constants.import_csv_log.status.'.$this->status),
            'no_of_rows' => (string) $this->no_of_rows,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
