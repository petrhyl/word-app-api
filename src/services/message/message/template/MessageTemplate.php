<?php

namespace services\message\message\template;

class MessageTemplate
{
    public const VARIABLE_SEPERATOR_START = '{*';
    public const VARIABLE_SEPERATOR_END = '*}';

    private string $filledBody;

    public function __construct(
        public readonly int $id,
        public readonly MessageTemplateType $type,
        public readonly string $subject,
        private readonly string $body,
        public readonly array $variables
    ) {
        $this->filledBody = $this->resolveVariables($body, $variables);
    }

    public function getBody(): string
    {
        return $this->filledBody;
    }

    public function resolveVariables(string $body, array $variables): string
    {
        $resolvedBody = $body;

        foreach ($variables as $key => $value) {
            $resolvedBody = str_replace(self::VARIABLE_SEPERATOR_START . $key . self::VARIABLE_SEPERATOR_END, $value, $resolvedBody);
        }

        return $resolvedBody;
    }
}
