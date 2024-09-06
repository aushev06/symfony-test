<?php

namespace EDM\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use EDM\Access;
use EDM\Enums\ContractType;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use OpenApi\Attributes as OA;

#[ORM\Entity]
#[ORM\Table(name: 'contract_applications')]
#[OA\Schema(description: 'Заявка на заключение договора поставки товара')]
class ContractApplication extends AbstractApplication
{
	#[Assert\NotNull]
	#[ORM\ManyToOne(targetEntity: Supplier::class, cascade: ['persist'], inversedBy: 'contract_applications')]
	#[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
	protected ?Supplier $supplier = null;

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'ФИО торгового представителя', type: 'string', example: 'Иванов Иван Иванович')]
	private string $representative_name = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Идентификатор торгового представителя', type: 'string', example: '')]
	private string $representative_code = '';

	#[ORM\Column(type: Types::STRING, enumType: ContractType::class)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Форма договора')]
	public ContractType $contract_type = ContractType::Buyer;

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Наименование организации', type: 'string', example: 'ООО Рога и Копыта Лимитед')]
	private string $applicant_full_name = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Полное наименование организации', type: 'string', example: 'Иванов Иван Иванович')]
	private string $contact_name = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'ФИО контактного лица организации', type: 'string', example: 'Рога и Копыта')]
	private string $applicant_name = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Номер телефона контактного лица организации', type: 'string', example: '+1-500-12-34')]
	private string $contact_phone = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Наименование банка организации ', type: 'string', example: 'Почта Банк')]
	private string $bank_name = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Номер счета в банке организации', type: 'string', example: '')]
	private string $bank_account = '';

	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Количество посадочных мест торговой точки', type: 'integer', example: 13)]
	private int $seats = 0;

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Описание вывески торговой точки', type: 'string', example: 'Шаверма без последствий')]
	private string $signboard = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Тип торговой точки', type: 'string', example: 'Шаурмячная')]
	private string $shop_type = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Вид кухни торговой точки', type: 'string', example: 'Стрит-фуд')]
	private string $cuisine_type = '';

	#[ORM\Column(type: Types::STRING, length: 255)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Формат клиента торговой точки', type: 'string', example: '')]
	private string $client_format = '';

	public function getSupplier(): ?Supplier
	{
		return $this->supplier;
	}

	public function setSupplier(?Supplier $supplier): static
	{
		$this->supplier = $supplier;
		return $this;
	}

	public function getRepresentativeName(): string
	{
		return $this->representative_name;
	}

	public function setRepresentativeName(string $representative_name): static
	{
		$this->representative_name = trim($representative_name);
		return $this;
	}

	public function getRepresentativeCode(): string
	{
		return $this->representative_code;
	}

	public function setRepresentativeCode(string $representative_code): static
	{
		$this->representative_code = trim($representative_code);
		return $this;
	}

	public function getContractType(): ContractType
	{
		return $this->contract_type;
	}

	public function setContractType(ContractType $contract_type): static
	{
		$this->contract_type = $contract_type;
		return $this;
	}

	public function getApplicantName(): string
	{
		return $this->applicant_name;
	}

	public function setApplicantName(string $applicant_name): static
	{
		$this->applicant_name = trim($applicant_name);
		return $this;
	}

	public function getApplicantFullName(): string
	{
		return $this->applicant_full_name;
	}

	public function setApplicantFullName(string $applicant_full_name): static
	{
		$this->applicant_full_name = trim($applicant_full_name);
		return $this;
	}

	public function getContactName(): string
	{
		return $this->contact_name;
	}

	public function setContactName(string $contact_name): static
	{
		$this->contact_name = trim($contact_name);
		return $this;
	}

	public function getContactPhone(): string
	{
		return $this->contact_phone;
	}

	public function setContactPhone(string $contact_phone): static
	{
		$this->contact_phone = trim($contact_phone);
		return $this;
	}

	public function getBankName(): string
	{
		return $this->bank_name;
	}

	public function setBankName(string $bank_name): static
	{
		$this->bank_name = trim($bank_name);
		return $this;
	}

	public function getBankAccount(): string
	{
		return $this->bank_account;
	}

	public function setBankAccount(string $bank_account): static
	{
		$this->bank_account = trim($bank_account);
		return $this;
	}

	public function getSeats(): int
	{
		return $this->seats;
	}

	public function setSeats(int $seats): static
	{
		$this->seats = max(0, $seats);
		return $this;
	}

	public function getSignboard(): string
	{
		return $this->signboard;
	}

	public function setSignboard(string $signboard): static
	{
		$this->signboard = trim($signboard);
		return $this;
	}

	public function getShopType(): string
	{
		return $this->shop_type;
	}

	public function setShopType(string $shop_type): static
	{
		$this->shop_type = trim($shop_type);
		return $this;
	}

	public function getCuisineType(): string
	{
		return $this->cuisine_type;
	}

	public function setCuisineType(string $cuisine_type): static
	{
		$this->cuisine_type = trim($cuisine_type);
		return $this;
	}

	public function getClientFormat(): string
	{
		return $this->client_format;
	}

	public function setClientFormat(string $client_format): static
	{
		$this->client_format = trim($client_format);
		return $this;
	}
}