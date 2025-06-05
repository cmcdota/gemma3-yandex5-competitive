from transformers import AutoTokenizer
from vllm import LLM, SamplingParams

model_name = "t-tech/T-lite-it-1.0"
tokenizer = AutoTokenizer.from_pretrained(model_name)
llm = LLM(model=model_name, max_model_len=8192)
sampling_params = SamplingParams(temperature=0.7,
                                repetition_penalty=1.05,
                                top_p=0.8, top_k=70)

prompt = "Сгенерируй пожалуйста формулу для рассчета показателя time to market"
messages = [
    {"role": "system", "content": "Ты виртуальный ассистент в Уралсиб банке. Твоя задача - быть полезным диалоговым ассистентом в задачах сотрудника банка"},
    {"role": "user", "content": prompt}
]

prompt_token_ids = tokenizer.apply_chat_template(messages, add_generation_prompt=True)

outputs = llm.generate(prompt_token_ids=prompt_token_ids, sampling_params=sampling_params)

generated_text = [output.outputs[0].text for output in outputs]
print(generated_text)
